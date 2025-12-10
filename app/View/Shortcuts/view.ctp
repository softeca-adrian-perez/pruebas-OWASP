<div class="row">
    <div class="row">
        <div class="columns medium-8 medium-offset-2">
            <h1 class="ta-center">
                <?php
                echo $this->Html->image(
                    FilePaths::SHORTCUT_IMAGES_RELATIVE.$shortcut['Shortcut']['image'],
                    array(
                        'alt' => $shortcut['Shortcut']['title'],
                        'title' => $shortcut['Shortcut']['title'],
                        'class' => 'logotipo',
                        'style' => 'height:50px'
                    )
                );
                echo $shortcut['Shortcut']['title'];
                ?>
            </h1>
        </div>
    </div>
    <hr/>
    <br />
    <div class="ta-center cnt-connect-favorite">
        <?php 
        if( $shortcut['Shortcut']['code'] == 'RM' )
        {
            if (REPAIR_MAINTENANCE_SEND_DATA) {
                echo $this->Form->create(null,
                    array(
                        'url' =>  $urlRM . Configure::read('repair-maintenance.url'),
                        'id' => 'repair-maintenance-login',
                        'target' => '_blank',
                    )
                ) .
                $this->Form->hidden(
                    '',
                    array(
                        'name' => 'datos',
                        'id' => 'datos',
                        'value' => $tokenLoginRM['datos'],
                    )
                ) .
                $this->Form->hidden(
                    '',
                    array(
                        'name' => 'check',
                        'id' => 'check',
                        'value' => $tokenLoginRM['check'],
                    )
                ) .
                $this->HTML->tag
                (
                    'input',
                    null,
                    array
                    (
                        'src' => $this->HTML->assetUrl('repair-maintenance.png?1', array('pathPrefix' => IMAGES_URL)),
                        'alt' => $shortcut['Shortcut']['title'],
                        'type' => 'image',
                    )
                ) .
                $this->Form->submit
                (
                    __t('General.Connect'),
                    array(
                        'class' => 'btn-guardar tres',
                    )
                ) .
                $this->Form->end();
            }
        } 
        else
        {
            ?>
            <form action='<?php echo $shortcut['Shortcut']['url'] ?>' method="POST" target="_blank">
                <?php
                if($shortcut['Shortcut']['is_sso'])
                {
                    ?> 
                    <input type="hidden" name='<?php echo $shortcut['Shortcut']['parameter_name_1'] ?>' value="<?php echo $this->request->data['Shortcut']['parameter_value_1'] ?>" id="username" autocapitalize="off">
                    <input type="hidden" name='<?php echo $shortcut['Shortcut']['parameter_name_2'] ?>' value="<?php echo $this->request->data['Shortcut']['parameter_value_2'] ?>" id="password" autocapitalize="off">
                    <?php
                }
                ?>
                <input type="checkbox" id="remember" name="savePassword" style="display:none">
                <input type="hidden"  name="_csrf"   value=""/>
                <input type="submit" style="margin:0px;" value="<?php echo __t('General.Connect') ?>" class="btn-guardar tres" />
            </form>
            <?php 
        }
        if( $shortcut['Shortcut']['code'] != 'RM' )
        {
            if (isset($shortcut_data['GarageDistributorShortcut']['fav']) && $shortcut_data['GarageDistributorShortcut']['fav'] == ConstantsBooleans::YES) 
            {
                $checked = true;
            }
            else
            {
                $checked = false;
            }
            echo $this->Form->input(
                'GarageDistributorShortcut.fav',
                array(
                    'type' => 'checkbox',
                    'label' => false,
                    'div' => false,
                    'value' => 1,
                    'checked' => $checked,
                    'hidden' => true,
                    'id' => 'shortcut_modal_' . $shortcut['Shortcut']['id']
                )
            ); 
            ?>
            <div class="d-inline-block cursor-pointer shortcut-favorite" data-url="
                <?php echo Router::url(
                    array(
                        'controller' => 'shortcuts',
                        'action' => 'ajax_edit_favorite'
                    )
                );?>"
                data-shortcut="<?php echo $shortcut['Shortcut']['id']; ?>">
                <label class="favorite_c-primary fw-bold unselectable" style="font-size: 1em !important">
                    <?php 
                    if($checked) 
                    {
                        ?> <span class="c-primary ion-ios-star unselectable <?php if ($checked) {echo 'c-primary';} ?>"></span> <?php
                    }
                    else
                    {
                        ?> <span class="c-primary ion-ios-star-outline unselectable <?php if ($checked) {echo 'c-primary';} ?>"></span> <?php
                    }
                    ?>
                    <?php echo __t('Shortcut.Favourite'); ?>
                </label>
            </div>
            <?php 
            if($shortcut['Shortcut']['is_sso'])
            {
                ?>
                <div class="row">
                    <div class="medium-12 columns m-top-1 fs-small ta-center">
                        <span class="cursor-pointer" id="remove_shortcut" data-id="<?php echo $this->request->data['Shortcut']['id']?>"><?php echo __t('General.Change_keys'); ?></span>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>



