<?php
echo $this->Html->script('services.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v='.Configure::read('VERSION_CACHE'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$checked_edit = CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? 'checked' : '';

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
            __t('Maintenance.Services'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) { ?>
            <li>
                <input id="tab-add" type="radio" class="d-none" name="tabs" checked>
                <label for="tab-add">
                    <?php echo __t('General.Add'); ?>
                </label>
            </li>
        <?php } ?>
        <li>
            <input id="tab-edit" type="radio" class="d-none" name="tabs" <?php echo $checked_edit; ?>>
            <label for="tab-edit">
                <?php echo __t('General.Edit'); ?>
            </label>
        </li>
    </ul>
</div>
<div class="cnt-data">
    <br />
    <div class="aag-padding" id="form_click">
        <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) { ?>
            <div id="form-add">
                <?php
                echo $this->Form->create(
                    'Service',
                    array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'url' => 'ajax_add_edit_service',
                        'id' => 'FormAddService'
                        )
                    ); ?>
                    <div class="aag-subtitle">
                        <?php echo __t('Maintenance.Service_add'); ?>
                    </div>
                    <div class="cnt-form-inputs">
                        <?php
                            foreach($languages as $languageCode) {
                                echo $this->Form->input(
                                    'Service.name_' . $languageCode,
                                    array(
                                        'label' => __t('Maintenance.Service_name_' . $languageCode),
                                        'type' => 'text',
                                        'id' => 'name-service-add-' . $languageCode,
                                        'required' => Configure::read('MAINTENANCES_DEFAULT_LANGUAGE') == $languageCode ? true : false
                                    )
                                );
                            }
                        ?>
                    </div>
                    <div>
                        <div class="columns medium-12 p-0">
                            <div class="p-vertical-1 columns medium-2">
                                <?php
                                echo $this->Html->image(
                                    'upload_pic.png',
                                    array(
                                        'alt' => __t('General.Img_not_loaded'),
                                        'class' => 'logotipo maintenace-img',
                                        'id' => 'img-service-add'
                                    )
                                );
                                ?>
                            </div>
                            <div class="p-vertical-1 columns medium-10" >
                                <?php
                                $cropper_options = array(
                                    "width" => "68",
                                    "height" => "32"
                                );
                                echo $this->Form->input(
                                    'Service.file',
                                    array(
                                        'id' => 'service-file-add',
                                        'class' => 'dragdrop-js crop-file-js',
                                        'label' => false,
                                        'type' => 'file',
                                        'multiple' => false,
                                        'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                        'div' => array(
                                            'class' => 'field_file cont-fileWrapper',
                                        ),
                                        'data-element_id' => 'image-service-file-add',
                                        'data-cropper_options' => json_encode($cropper_options),
                                        'data-width' => ConstantsServiceImageDimension::WIDTH,
                                        'data-height' => ConstantsServiceImageDimension::HEIGHT,
                                    )
                                );
                                echo $this->Form->hidden(
                                    'new_image',
                                    array(
                                        'id' => 'image-service-file-add',
                                    )
                                )
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="clear ta-right m-top-1">
                        <?php
                    echo $this->Form->Button(
                        __t('General.Save'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'add-service'
                            )
                        );
                        ?>
                    </div>
                <?php echo $this->Form->end(); ?>
            </div>
        <?php } ?>
        <div id="form-edit" class='d-none'>
            <?php
            echo $this->Form->create(
                '',
                array(
                    'class' => 'form-horizontal',
                    'enctype' => 'multipart/form-data',
                    'id' => 'FormEditService',
                    'url' => 'ajax_add_edit_service'
                )
            );
                ?>
                <div class="aag-subtitle">
                    <?php echo __t('Maintenance.Service_edit'); ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
						foreach($languages as $languageCode) {
							echo $this->Form->input(
								'Service.name_' . $languageCode,
								array(
									'label' => __t('Maintenance.Service_name_' . $languageCode),
									'type' => 'text',
									'id' => 'name-service-edit-' . $languageCode,
									'required' => Configure::read('MAINTENANCES_DEFAULT_LANGUAGE') == $languageCode ? true : false
								)
							);
						}
                    ?>
                </div>
                <div class="row">
                    <div class="p-vertical-1 columns medium-2">
                        <?php
                        echo $this->Html->image(
                            'upload_pic.png',
                            array(
                                'alt' => __t('General.Img_not_loaded'),
                                'class' => 'logotipo maintenace-img',
                                'id' => 'img-service-edit'
                            )
                        );
                        ?>
                    </div>
                    <div class="p-vertical-1 columns medium-10" >
                        <?php
                        $cropper_options = array(
                            "width" => "68",
                            "height" => "32"
                        );
                        echo $this->Form->input(
                            'Service.file',
                            array(
                                'id' => 'service-file-edit',
                                'class' => 'dragdrop-js crop-file-js',
                                'label' => false,
                                'type' => 'file',
                                'multiple' => false,
                                'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                'div' => array(
                                    'class' => 'field_file cont-fileWrapper',
                                ),
                                'data-element_id' => 'image-service-file-edit',
                                'data-cropper_options' => json_encode($cropper_options),
                                'data-width' => ConstantsServiceImageDimension::WIDTH,
                                'data-height' => ConstantsServiceImageDimension::HEIGHT,
                            )
                        );
                        echo $this->Form->hidden('new_image', array('id' => 'image-service-file-edit'));
                        ?>
                    </div>
                </div>
                <div class="clear ta-right m-top-1">
                    <?php
                    echo $this->Form->button(
                        __t('General.Cancel'),
                        array(
                            'type' => 'button',
                            'class' => 'aag-button medium four',
                            'id' => 'unselect-service',
                            'disabled' => true,
                            'data-super_admin' => CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ? true : false
                        )
                    );
                    echo $this->Form->Button(
                        __t('General.Edit'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'edit-service'
                        )
                    );
                    ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div id="form-edit-msg" class=<?php echo CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ? 'd-none' : '';?>>
            <div class="aag-subtitle">
                <?php echo __t('Maintenance.Service_edit'); ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
					foreach($languages as $languageCode) {
						echo $this->Form->input(
							'',
							array(
								'label' => __t('Maintenance.Service_name_' . $languageCode),
								'type' => 'text',
								'disabled' => true,
							)
						);
					}
                ?>
            </div>
            <div class="row">
                <div class="columns medium-12 p-0">
                    <?php echo __t('Maintenance.Dimensions'); ?></h6></div>
                    <div class="p-vertical-1 columns medium-2">
                        <?php
                        echo $this->Html->image(
                            'upload_pic.png',
                            array(
                                'alt' => __t('General.Img_not_loaded'),
                                'class' => 'logotipo maintenace-img',
                                'id' => 'img-service-edit-msg'
                            )
                        );
                        ?>
                    </div>
                    <div class="p-vertical-1 columns medium-10" >
                        <?php
                        $cropper_options = array(
                            "width" => "68",
                            "height" => "32"
                        );
                        echo $this->Form->input(
                            '',
                            array(
                                'id' => 'service-file-edit-msg',
                                'class' => 'dragdrop-js crop-file-js',
                                'label' => false,
                                'disabled' => true,
                                'type' => 'file',
                                'multiple' => false,
                                'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                'div' => array(
                                    'class' => 'field_file cont-fileWrapper',
                                ),
                                'data-element_id' => 'image-service-file-add',
                                'data-cropper_options' => json_encode($cropper_options),
                                'data-width' => ConstantsServiceImageDimension::WIDTH,
                                'data-height' => ConstantsServiceImageDimension::HEIGHT,
                            )
                        );
                        ?>
                        <div class="columns medium-12 ta-center" style="padding-top:3rem">
                            <div class="aag-title"><?php echo __t('Maintenance.Service_select'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="ajax_table_services" class="aag-padding">
        <?php echo $this->element('../Services/Elements/table_services'); ?>
    </div>
    </div>
</div>
<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true"
     role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>