<?php  echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach( $websites as $website )
    {
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="website_<?php echo $website['Website']['id'] ?>" data-id="<?php echo $website['Website']['id'] ?>">
            <div id="language">
                <?php echo h($website['Website']['name']); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-website-js lh-1',
                        'data-confirmmsg' => __t('Maintenance.Website_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'websites',
                            'action' => 'ajax_delete_website',
                            $website['Website']['id'],
                        )),
                        'data-id' => $website['Website']['id'],
                        'data-name' => $website['Website']['name'],
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