<div class="medium-12" id="session-msg"><?php  echo $this->Session->flash(); ?></div>
<div class="medium-12 columns m-top-1">
    <?php foreach( $positions as $position ){?>
        <div class="columns medium-4 select_tr end" id="position_<?php echo $position['Position']['id'] ?>"
             data-id="<?php echo $position['Position']['id'] ?>"
             data-role-id="<?php echo $position['Position']['role_id'];?>">
            <div class="columns medium-11  p-0">
                <div id="language-en" <?php if($selected_language != 'name_en'){ echo "class='d-none'";}?>>
                    <?php echo h($position['Position']['name_en']); ?>
                </div>
                <div id="language-fr" <?php if($selected_language != 'name_fr'){ echo "class='d-none'";}?>>
                    <?php echo h($position['Position']['name_fr']); ?>
                </div>
                <div id="language-de" <?php if($selected_language != 'name_de'){ echo "class='d-none'";}?>>
                    <?php echo h($position['Position']['name_de']); ?>
                </div>
            </div>
            <div class="columns medium-1">
                <?php echo $this->Html->link(
                    '<span class="ion-trash-b c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-position-js',
                        'data-confirmmsg' => __t('Maintenance.Position_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'positions',
                            'action' => 'ajax_delete_position',
                            $position['Position']['id'],
                        )),
                        'data-id' => $position['Position']['id'],
                        'data-name' => $position['Position'][$selected_language],
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                ); ?>
            </div>
        </div>
    <?php } ?>
</div>