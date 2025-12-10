<?php
    echo $this->Html->script('config.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
    echo $this->Form->create(
        'Config',
        array(
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'id' => 'config_default_form',
            'data-url' => Router::url(array(
                'controller' => 'config',
                'action' => 'ajax_save_config'
            )),
            'data-url-module' => Router::url(array(
                'controller' => 'config',
                'action' => 'ajax_save_module_config'
            )),
        )
    );
?>
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
                __t('Configuration.Configuration'),
                array(
                    'controller' => 'config',
                    'action' => 'home'
                )
            ),
            __t('Home Config'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<?php
if ($user_role_id == ConstantsRoles::SUPER_ADMIN) {
?>
    <div class="cnt-data aag-padding p-vertical-1">
        <div id="cuerpo-module-access" class="columns medium-12">
            <div class="columns medium-12 background-color-blanco p-top-1 p-left-0 p-right-0">
                <div>
                    <?php
                    echo $this->Html->link(
                        __t('Module Configuration'),
                        array(
                            'controller' => 'config',
                            'action' => 'home?tab=2',
                        ),
                        array(
                            'id' => 'module-config-js',
                            'class' => 'aag-button medium one outlined',
                            'escape' => false,
                        )
                    );
                    ?>
                </div>
                <div class="columns medium-6 required" style="display:inline;">
                    <table class="table-tracking table-config" id="modules-config-js">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t('Configuration.Configuration'); ?>
                                </th>
                                <th class="ta-center" width="150">
                                    <?php echo __t('General.Active'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($aag_config as $key => $section) {
                            ?>
                                <?php if ($key == ConstantsSections::MODULES) {?>
                                    <?php foreach ($section['Sections'] as $key_section => $field) {?>
                                        <tr>
                                            <td style="padding: .5rem .625rem .625rem .625rem;" title="<?php echo h($field['tooltip' . __s()]); ?>">
                                                <?php echo h($field['name' . __s()]); ?>
                                            </td>
                                            <td style="padding: .5rem .625rem .625rem .625rem;" class="ta-center" title="<?php echo $field['tooltip' . __s()]; ?>">
                                                <?php
                                                    $id = $field['id'];
                                                    if ($field['active']) {
                                                ?>
                                                    <span class="ico-toggle ion-toggle-filled c-exito icono-grande default" data-id="<?php echo $field['id']?>"></span>
                                                <?php } else { ?>
                                                    <span class="ico-toggle ion-toggle c-fallo icono-grande default" data-id="<?php echo $field['id']?>"></span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>