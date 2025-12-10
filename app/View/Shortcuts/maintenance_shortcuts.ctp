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
            __t('Shortcut.List'),
        ));
        ?>
    </div>
    <div>
    	<a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            // echo $this->Html->link(__t('Maintenance.Maintenance_home'), array('controller' => 'maintenance', 'action' => 'home'));
            echo $this->Html->link(
                __t('Shortcut.New_shortcut'),
                array(
                    'controller' => 'shortcuts',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        } ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Shortcuts/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo __t('Shortcut.Image'); ?></th>
                <th><?php echo $this->Paginator->sort('Shortcut.title', __t('Shortcut.Title')); ?></th>
                <th><?php echo __t('Network.Networks'); ?></th>
                <!-- <th><?php echo __t('Shortcut.Roles'); ?></th> -->
                <th class="ta-center"><?php echo $this->Paginator->sort('Shortcut.start_date', __t('Shortcut.Start_date')); ?></th>
                <th class="ta-center"><?php echo __t('Shortcut.End_date'); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Shortcut.active', __t('General.Active')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($shortcuts as $shortcut) { ?>
                <tr>
                    <td>
                        <img
                            src="<?php echo FileManager::get_url(FilePaths::SHORTCUT_IMAGES_RELATIVE . $shortcut['Shortcut']['image']); ?>"
                            style="max-width: 100px">
                    </td>
                    <td>
                        <?php 
                        if(CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN){
                            echo $this->Html->link(
                                $shortcut['Shortcut']['title'],
                                array(
                                    'controller' => 'shortcuts',
                                    'action' => 'software_view',
                                    $shortcut['Shortcut']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        }else{
                            echo $this->Html->link(
                                $shortcut['Shortcut']['title'],
                                array(
                                    'controller' => 'shortcuts',
                                    'action' => 'edit',
                                    $shortcut['Shortcut']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        }
                         ?>
                    </td>
                    <td>
                        <?php
                        $networks = explode(",", $shortcut[0]['Networks']);
                        if (!empty($shortcut[0]['Networks'])) {
                            foreach ($networks as $network) { ?>
                                <div>
                                    <?php echo h($networks_list_all[$network]); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <!-- <td>
                        <?php
                        $roles = explode(",", $shortcut[0]['Roles']);
                        if (!empty($shortcut[0]['Roles'])) {
                            foreach ($roles as $role) { ?>
                                <div>
                                    <?php echo h($roles_list[$role]); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td> -->
                    <td class="ta-center">
                        <?php echo Fecha::toFormatoVistaFecha(h($shortcut['Shortcut']['start_date'])); ?>
                    </td>
                    <td class="ta-center">
                        <?php echo Fecha::toFormatoVistaFecha(h($shortcut['Shortcut']['end_date'])); ?>
                    </td>
                    <?php
                    $font = 'c-fallo';
                    if ($shortcut['Shortcut']['active']) {
                        $font = 'c-exito';
                    } ?>
                    <td class="ta-center <?php echo $font; ?>">
                        <?php
                        echo ($shortcut['Shortcut']['active']) ? __t('General.Yes') : __t('General.No');
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>