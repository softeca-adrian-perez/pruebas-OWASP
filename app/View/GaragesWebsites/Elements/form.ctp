<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GarageWebsite',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);

echo $this->Form->hidden('GarageWebsite.id');
echo $this->Form->hidden('GarageWebsite.garage_id');
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Garage.Marketing') . ' / ' . __t('Garage.Images'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_marketing_and_image_garage',
                        $garage_id
                    )
                ),
                __t('Maintenance.Website_new'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Garage.Marketing') . ' / ' . __t('Garage.Images'),
                    array(
                        'controller' => 'garages',
                        'action' => 'add_marketing_and_image_garage',
                        $garage_id
                    )
                ),
                CakeSession::read('Auth.User.edit_enabled') ? __t('General.Edit') : __t('General.View'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        );
        if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
            <div class="f-right">
                <button type="button" id="edit-btn-disable" value="1" class="aag-button medium"
                    data-url="<?php echo Router::url(
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'ajax_update_edit',
                                    )
                                ); ?>"
                    data-edit="<?php echo __t('General.Edit'); ?>"
                    data-view="<?php echo __t('General.View'); ?>"
                    data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
                    <?php echo __t('General.Edit'); ?>
                </button>
            </div>
        <?php } ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Maintenance.Website_new');
        } else {
            echo $websites[$this->request->data['GarageWebsite']['website_id']];
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        echo $this->Form->input(
            'website_id',
            array(
                'label' => __t('Maintenance.Website'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $websites,
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'url',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Maintenance.URL'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'tagline',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Maintenance.Tagline'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'description',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Maintenance.Description'),
                'disabled' => $this->request->action == 'add' ? false : true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
    <?php if (isset($garage_website_id)) { ?>
        <div class="ta-right btn-hide m-top-1" hidden>
            <?php
            echo $this->Html->link(
                __t('General.Delete'),
                array(),
                array(
                    'escape' => false,
                    'title' => __t('General.Delete'),
                    'class' => 'aag-button medium red swal-msg',
                    'data-confirmmsg' => __t('Maintenance.Website_delete?'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                    'data-type' => 'warning',
                    'data-url' => Router::url(array(
                        'controller' => 'garages_websites',
                        'action' => 'delete',
                        $garage_id,
                        $garage_website_id,
                    )),
                )
            );
            ?>
        </div>
    <?php } ?>
</div>
</div>
</div>
</div>

<?php echo $this->Form->end(); ?>