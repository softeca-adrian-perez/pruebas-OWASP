<?php
$config = CakeSession::read('Auth.User.Config');

echo $this->Html->script('gallery_garages.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));

echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('provinces_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('campaign_table.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>

<?php
echo $this->Form->create(
    'Garage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'type' => 'post',
    )
);

echo $this->Form->hidden('Garage.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('Garage.Marketing') . ' / ' . __t('Garage.Images'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium"
                data-url="<?php echo Router::url(
                                array(
                                    'controller' => 'garages',
                                    'action' => 'ajax_update_edit',
                                )
                            ); ?>"
                data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit') ?>
            </button>
        <?php } ?>
        <div class="f-right btn-hide" hidden>
            <?php
            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                echo $this->element(
                    'Comun/form_actions_garage',
                    $cancel_action
                );
            } else if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)) {
                echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'aag-button medium',
                    )
                );
            }
            ?>
        </div>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'marketing_and_image_garage')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']);?>
    </div>
    <div class="btn-hide" hidden>
        <div class="cnt-form-inputs m-top-1">
            <?php echo $this->Form->input(
                'campaign_entries',
                array(
                    'label' => __t('Garage.Campaign_entries'),
                    'type' => 'select',
                    'class' => 'select2-multiple input-disabled',
                    'id' => 'select-campaign',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $campaign_entries,
                )
            );
            echo $this->Form->input(
                'number',
                array(
                    'label' => __t('Garage.Quantity'),
                    'id' => 'input-number',
                    'type' => 'number',
                )
            ); ?>
            <div style="display:inline !important">
                <?php echo $this->Html->link(
                    __t('General.Add'),
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green f-right',
                        'id' => 'add-campaign',
                        'data-add' => Router::url(array(
                            'controller' => 'garages',
                            'action' => 'ajax_save_new_value_campaign',
                            $garage['Garage']['id']
                        )),
                        'data-reload' => Router::url(array(
                            'controller' => 'garages',
                            'action' => 'ajax_get_values_list_campaign',
                            $garage['Garage']['id']
                        )),
                        'data-delete' => Router::url(array(
                            'controller' => 'garages',
                            'action' => 'ajax_delete_value_campaign'
                        )),
                    )
                ); ?>
            </div>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive" id="tabla-campaign">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Campaign_entries') ?></th>
                    <th><?php echo __t('Garage.Quantity') ?></th>
                    <th class="btn-hide" hidden><?php echo __t('General.Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $key => $campaign) { ?>
                    <tr>
                        <td><?php echo $campaign['CampaignEntry']['name_en'] ?></td>
                        <td><?php echo ($campaign['GarageCampaign']['number'] ? $campaign['GarageCampaign']['number'] : 0) ?></td>
                        <td class="btn-hide" hidden><span class="delete-campaign aag-icon-papelera c-fallo cursor-pointer" data-key="<?php echo $campaign['GarageCampaign']['id'] ?>"></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($config[ConstantsConfig::MARKETING_EMAIL]) { ?>
        <div class="aag-subtitle">
            <?php echo __t('Garage.Miscellaneous'); ?>
        </div>
        <?php
        if ($config[ConstantsConfig::MARKETING_EMAIL]) { ?>
            <div class="large-6 medium-12 columns end">
                <?php echo $this->Form->input(
                    'marketing_email',
                    array(
                        'type' => 'text',
                        'required' => true,
                        'label' => __t('Garage.Marketing_email'),
                        'disabled' => true,
                        'class' => 'input-disabled',
                    )
                ); ?>
            </div>
    <?php }
    } ?>
    <div class="aag-subtitle">
        <?php echo __t('Garage.Websites'); ?>
    </div>
    <div class="btn-hide" hidden>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php
                echo $this->Html->link(
                    __t('Maintenance.Website_new'),
                    array(
                        'controller' => 'garages_websites',
                        'action' => 'add',
                        $garage_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                        'style' => 'position:relative; z-index: 1;',
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Website'); ?></th>
                    <th><?php echo __t('Garage.URL'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_websites as $garage_website) { ?>
                    <tr>
                        <td class="link-text">
                            <?php echo $this->Html->link(
                                $websites[$garage_website['GarageWebsite']['website_id']],
                                array(
                                    'controller' => 'garages_websites',
                                    'action' => 'edit',
                                    $garage_website['GarageWebsite']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); ?>
                        </td>
                        <td>
                            <?php echo h($garage_website['GarageWebsite']['url']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Garage.Images'); ?>
    </div>
    <div class="grid-x cnt-carousel-garage-marketing" style="margin-left: -15px; margin-right: -15px;">
        <div class="cell medium-6 p-1">
            <?php echo $this->element('../GaragesImages/Elements/gallery'); ?>
        </div>
        <div class="cell medium-6 p-1">
            <div class="row btn-hide" hidden>
                <div class="columns medium-10 pointer-disabled" style="pointer-events: none; cursor: not-allowed">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                    <?php
                    echo $this->Form->input(
                        'GarageImage.files.',
                        array(
                            'id' => 'image-input',
                            'class' => 'dragdrop-js dragdrop-multiple-js dragdrop-v2-js',
                            'data-images_upload' => count($images),
                            'data-max_images' => null,
                            'data-max_images_message' => '',
                            'label' => false,
                            'type' => 'file',
                            'multiple' => false,
                            'disabled' => false,
                            'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                            'div' => array(
                                'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                            ),
                        )
                    );
                    echo $this->Form->hidden('GarageImage.new_image', array('id' => 'new-image-input'));
                    ?>
                </div>
            </div>
            <?php if (
                !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
                $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
            ) { ?>
                <div class="row p-top-1">
                    <div class="columns medium-7">
                        <div class="aag-subtitle">
                            <?php echo __t('RequestedChanges.Describe_change'); ?>
                        </div>
                    </div>
                    <div class="medium-12 columns end">
                        <?php
                        echo $this->Form->input(
                            'ChangeDescription',
                            array(
                                'type' => 'text',
                                'name' => 'change_description',
                                'rows' => 7,
                                'label' => false,
                                'disabled' => true,
                                'class' => 'input-disabled',
                            )
                        );
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary"
        data-reveal
        aria-labelledby="modalTitle"
        aria-hidden="true"
        role="dialog"
        data-options="close_on_background_click:false">
        <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
        <img id="image-to-crop" alt="crop" src="" />
        <p class="text-center m-0-i p-top-1">
            <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
        </p>
        <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
    </div>