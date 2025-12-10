<div class="row">
    <?php
    echo $this->Form->create(
        'Shortcut',
        array(
            'id' => 'ShortcutForm',
            'data-id' => isset($shortcut) ? $shortcut['Shortcut']['id'] : null
        )
    );
    ?>
    <div>
        <div class="row">
            <div class="columns medium-8 medium-offset-2">
                <h1 class="ta-center">
                    <?php if (isset($shortcut)) {
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut['Shortcut']['image']),
                            array(
                                'alt' => $shortcut['Shortcut']['title'],
                                'title' => $shortcut['Shortcut']['title'],
                                'class' => 'logotipo',
                                'style' => 'height:50px'
                            )
                        );
                        echo h($shortcut['Shortcut']['title']);
                    } ?>
                </h1>
            </div>
        </div>
        <hr/>
        <div class="row m-vertical-1">
            <div class="columns medium-8 medium-offset-2">
                <?php
                echo $this->Form->input(
                    'Shortcut.parameter_value_1',
                    array(
                        'placeholder' => __t('User.User'),
                        'label' => __t('User.User'),
                        'type' => 'text',
                    )
                );
                ?>
            </div>
        </div>
        <div class="row m-vertical-1">
            <div class="columns medium-8 medium-offset-2">
                <?php
                echo $this->Form->input(
                    'Shortcut.parameter_value_2',
                    array(
                        'placeholder' => __t('General.Password'),
                        'label' => __t('General.Password'),
                        'type' => 'password',
                    )
                );
                ?>
            </div>
        </div>
    </div>

    <div class="row ta-center">
        <input type="button" onclick="SaveShortcut(event)" title="<?php echo __t('General.Save') ?>"
               value="<?php echo __t('General.Save') ?>" style="margin:0" class="btn-guardar tres"/>
    </div>

    <?php echo $this->Form->end(); ?>

</div>
