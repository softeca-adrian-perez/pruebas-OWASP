<?php
echo $this->Html->script('gallery_garages.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v='.Configure::read('VERSION_CACHE'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE,ConstantsRoles::DISTRIBUTOR))?'my_data':'view';
echo $this->Form->create(
    'DistributorImage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'type' => 'post',
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                $distributor['Distributor']['name'],
                array(
                    'controller' => 'distributors',
                    'action' => $action,
                    $distributor['Distributor']['id']
                )
            ),
            __t('Distributor.Image'),
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs',array('selected' => 'image_distributor',)); ?>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
    </div>
    <div class="cnt-data-element m-top-1">
        <div class="aag-subtitle">
            <?php echo __t('Distributor.Images');?>
        </div>
        <div class="grid-x cnt-carousel-garage-marketing" style="margin-left: -15px; margin-right: -15px;">
        <div class="cell medium-6 p-1">
        <?php
        echo $this->element('../DistributorsImages/Elements/gallery'); ?>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
        </div>
        <div class="cell medium-6 p-1">
        <?php
        echo $this->Form->input(
            'DistributorImage.files.',
            array(
                'id' => 'image-input',
                'class' => 'dragdrop-js dragdrop-multiple-js dragdrop-v2-js',
                'data-images_upload' => count($images),
                'data-max_images' => null,
                'data-max_images_message' => '',
                'label' => false,
                'type' => 'file',
                'multiple' => true,
                'disabled' => false,
                'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                'div' => array(
                    'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                ),
            )
        );
        echo $this->Form->hidden(
            'DistributorImage.new_image',
            array(
                'id' => 'new-image-input',
            )
        );
        ?>
        <?php  if(
            !$this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] ) &&
            $this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )
        ){ ?>
            <div class="columns medium-7">
                <div class="titulo2">
                    <?php echo __t('RequestedChanges.Describe_change'); ?>
                </div>
            </div>
            <div class="medium-5 columns ta-right cnt-buttons-v2">
                <?php echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'btn-edit edit',
                    )
                );
                ?>
            </div>
            <div class="medium-12 columns end">
            <?php
                echo $this->Form->input(
                    'ChangeDescription',
                    array(
                        'type' => 'text',
                        'name' => 'change_description',
                        'rows' => 7,
                        'label' => false
                    )
                );
            ?>
            </div>
        <?php } ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    </div>
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