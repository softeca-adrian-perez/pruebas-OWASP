<div id="div_users_permissions">
    <?php echo $this->Form->create(
        'Search',
        array(
            'id' => 'search-permissions-js',
            'class' => 'buscador',
            'type' => 'get',
            'url' => array(
                'controller' => 'garages',
                'action' => 'ajax_search_users_garage_permissions',
            ),
        )
    );
    ?>
    <div class="flex ai-end">
        <div class="fg-1">
            <?php echo $this->Form->input(
                'permission_id',
                array(
                    'label' => __t('General.List_of_permissions'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'empty' => false,
                    'options' => $garages_permissions,
                    'id' => 'permission_list'
                )
            ); ?>
        </div>
        <?php
        echo $this->Html->link(
            __t('General.Search'),
            '#',
            array(
                'escape' => true,
                'class' => 'search-users-permissions-garages-js aag-button small m-bottom-1',
                'data-url' => Router::url(array(
                    'controller' => 'garages',
                    'action' => 'ajax_search_users_garage_permissions',
                )),
                'div' => false,
                'data-div_users_permissions' => '#div_users_permissions',
                'title' => __t('General.Search'),
            )
        );
        ?>
    </div>
    <?php echo $this->Form->end(); ?>
    <div class="list_permissions-js">
        <div class="o-auto modal-permissions">
            <table class="tabla2">
                <thead>
                    <tr>
                        <th><?php echo __t('User.Name'); ?></th>
                        <th><?php echo __t('User.Surname'); ?></th>
                        <th><?php echo __t('Position.Position'); ?></th>
                        <th><?php echo __t('Role.Role'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if( !empty( $users_garage_permissions ) ){ ?>
                        <?php foreach ($users_garage_permissions as $user){?>
                            <tr>
                                <td>
                                    <?php echo h($user['User']['name']); ?>
                                </td>
                                <td>
                                    <?php echo h($user['User']['surname']); ?>
                                </td>
                                <td>
                                <?php echo h($positions[$user['Contact']['position_id']]); ?>
                                </td>
                                <td>
                                    <?php echo h($roles[$user['User']['role_id']]); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
