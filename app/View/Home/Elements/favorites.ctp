
<?php
echo $this->Form->hidden(
    'favorites_number',
    array(
        'value' => count($favorites_shortcuts)
    )
);
foreach($favorites_shortcuts as $shortcut){ ?>
    <div class="columns medium-3 cnt-favorite p-left-0 end">
        <div class="columns medium-12 cnt-favorite-shortcut lh-1" data-id="<?php echo $shortcut['Shortcut']['id'];?>">
            <img src="<?php echo FilePaths::SHORTCUT_IMAGES_RELATIVE.$shortcut['Shortcut']['image']; ?>" style="max-width: 100px;">
            <?php
            //            <span class="ion-ios-arrow-thin-right arrow-shortcut" style="margin-left: auto !important;"></span>
            echo h($shortcut['Shortcut']['title']);
            ?>
        </div>
        <?php echo $this->Html->link(
            '',
            array(
                'controller' => 'shortcuts',
                'action' => 'ajax_view',
                $shortcut['Shortcut']['id']
            ),
            array(
                'escape' => false,
                'class' => 'open-modal-js logotipo',
                'data-open' => 'myModalSSO',
                'id' => 'fav_shortcut_' . $shortcut['Shortcut']['id'],
                'hidden' => true,
                'alt' => $shortcut['Shortcut']['title'],
                'title' => $shortcut['Shortcut']['tooltip'],
            )
        ); ?>
    </div>
<?php } ?>


