<?php
echo $this->Html->script('tutorials.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$user = CakeSession::read('Auth.User.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Menu.User_guides'),
                array(
                    'controller' => 'user_guides',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER_GUIDE)) {
            echo $this->Html->link(
                __t('Tutorial.Add_user_guides'),
                array(
                    'controller' => 'user_guides',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data fg-0 p-top-1">
    <div class="aag-title cnt-data-element m-bottom-1">
        <?php echo __t('Menu.User_guides'); ?>
    </div>
    <div class="cnt-data-element clear cnt-form-animate" id="results_table_tutorial_ajax">
        <div class="sort_videos cnt-videos"
            id="<?php if ($role_id == ConstantsRoles::ADMIN || $role_id == ConstantsRoles::SUPER_ADMIN) {
                    echo 'SortVideos';
                } ?>"
            data-url="<?php echo Router::url(array(
                            'controller' => 'user_guides',
                            'action' => 'ajax_load_more',
                        )) ?>"
            data-url-sort="<?php echo Router::url(array(
                                'controller' => 'user_guides',
                                'action' => 'ajax_set_order',
                            )) ?>"
            data-page="<?php echo ConstantsPagination::FIRST_PAGE; ?>">
            <?php echo $this->element('../UserGuides/Elements/results_table'); ?>
        </div>
    </div>
</div>
</div>