<?php echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach($associations as $association )
    {
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="association_<?php echo $association['Association']['id'] ?>" data-id="<?php echo $association['Association']['id'] ?>">
            <div id="language">
                <?php echo h($association['Association']['name']); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-association-js lh-1',
                        'data-confirmmsg' => __t('Maintenance.Association_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'associations',
                            'action' => 'ajax_delete_association',
                            $association['Association']['id'],
                        )),
                        'data-id' => $association['Association']['id'],
                        'data-name' => $association['Association']['name'],
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                );
            } ?>
        </div>
        <?php
    }
    ?>
</div>