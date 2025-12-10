<?php 
echo $this->Html->script('websites.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('Maintenance.Website'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <li>
            <input id="tab-add" type="radio" class="d-none" name="tabs" checked>
            <label for="tab-add">
                <?php echo __t('General.Add'); ?>
            </label>
        </li>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
            <li>
                <input id="tab-edit" type="radio" class="d-none" name="tabs">
                <label for="tab-edit">
                    <?php echo __t('General.Edit'); ?>
                </label>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="cnt-data">
    <br />
    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
    <div class="aag-padding" id="form_click">
        <div id="form-add">
            <?php
            echo $this->Form->create(
                'Website',
                array(
                    'url' => 'ajax_add_website',
                    'id' => 'FormAddWebsite'
                )
            ); ?>
                <div class="aag-subtitle"><?php echo __t('Maintenance.Website_add'); ?></div>
                <div class="cnt-form-inputs">
                    <?php
                    echo $this->Form->input(
                        'Website.name',
                        array(
                            'label' => __t('Maintenance.Website_name'),
                            'type' => 'text',
                            'id' => 'name-website-add'
                        )
                    );
                    ?>
                </div>
                <div class="clear ta-right m-top-1">
                    <?php echo $this->Form->Button(
                        __t('General.Save'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'add-website',
                        )
                    ); ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div id="form-edit" class="d-none">
            <?php
            echo $this->Form->create(
                '',
                array(
                    'id' => 'FormEditWebsite',
                    'url' => 'ajax_edit_website'
                )
            ); ?>
                <div class="aag-subtitle"><?php echo __t('Maintenance.Website_edit'); ?></div>
                <div class="cnt-form-inputs">
                    <?php echo $this->Form->input(
                        'Website.name',
                        array(
                            'label' => __t('Maintenance.Website_name'),
                            'type' => 'text',
                            'id' => 'name-website-edit'
                        )
                    ); ?>
                </div>
                <div class="clear ta-right m-top-1">
                    <?php
                    echo $this->Form->button(
                        __t('General.Cancel'),
                        array(
                            'type' => 'button',
                            'class' => 'aag-button medium four',
                            'id' => 'unselect-website',
                            'disabled' => true
                        )
                    );
                    echo $this->Form->Button(
                        __t('General.Edit'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'edit-website'
                        )
                    );
                    ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div id="form-edit-msg" class="d-none">
            <div class="aag-subtitle"><?php echo __t('Maintenance.Website_edit'); ?></div>
            <div class="cnt-form-inputs">
                <?php
                echo $this->Form->input(
                    '',
                    array(
                        'label' => __t('Maintenance.Website_name'),
                        'type' => 'text',
                        'disabled' => true
                    )
                );
                ?>
            </div>
            <div class="aag-subtitle"><?php echo __t('Maintenance.Website_select'); ?></div>
        </div>
    </div>
    <?php } ?>
    <div id="ajax_table_websites" class="aag-padding">
        <?php echo $this->element('../Websites/Elements/table_websites'); ?>
    </div>
</div>