<?php
echo $this->Html->script('gallery_garages.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('lib/jscolor.min.js', array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v='.Configure::read('VERSION_CACHE'));
echo $this->Form->create(
    'GarageNetworkImage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="aag-padding">
        <div class="grid-x cnt-carousel-garage-marketing" style="margin-left: -15px; margin-right: -15px;">
            <div class="cell medium-6 p-1">
                <div class="aag-subtitle">
                    <?php echo __t('Garage.Images'); ?>
                </div>
                <?php echo $this->element('../GaragesNetworks/Elements/gallery'); ?>
            </div>
			<div class="cell medium-6 p-1">
				<div class="row">
					<div class="container-message">
						<span class="warning-message">
							<img src="/img/warning.svg" width="25" alt="Warning" title="warning" />
							<?php echo __t('General.Format_images'); ?>
							<br/>
							<?php echo __t('General.Other_format_images'); ?>
						</span>
					</div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
					<?php
					echo $this->Form->input(
						'GarageNetworkImage.files.',
						array(
							'id' => 'image-input',
							'class' => 'dragdrop-js dragdrop-multiple-js dragdrop-v2-js',
							'data-images_upload' => count($images),
							'data-max_images' => in_array($network_id, array(NETWORK_ID_GV, NETWORK_ID_GC)) ? 2 : null,
							'data-max_images_message' => in_array($network_id, array(NETWORK_ID_GV, NETWORK_ID_GC)) ? __t('General.Max_drop_files') : '',
							'label' => false,
							'type' => 'file',
							'multiple' => false,
							'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
							'before' => '<div class="text-drop">'.__t('General.Drop_files').'</div>',
							'div' => array(
								'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
							),
						)
					);
					echo $this->Form->hidden('new_image', array('id' => 'new-image-input'));
					?>
				</div>
			</div>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<style>
    ul.orbit-container {
        min-height: 210px;
        margin-bottom: 7px;
    }
    button.orbit-previous, button.orbit-next {
        margin-top: -50px;
    }
    div.cell:has(.field_file) {
        padding-top: 46px;
    }
</style>