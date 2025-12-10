<?php
$action = $this->request->action;
echo $this->Html->script('communications.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Form->create(
    'Communication',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);

echo $this->Form->hidden('Communication.id');
echo $this->Form->hidden(
    'CommunicationSection.section_subsection_id',
    array(
        'id' => 'subsection_id'
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Communication.Communications'),
                    array(
                        'controller' => 'communications',
                        'action' => 'maintenance_communications'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Communication.Communications'),
                    array(
                        'controller' => 'communications',
                        'action' => 'maintenance_communications'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Communication.Communication'); ?>
    </div>
    <div class="cnt-two-columns m-top-1">
        <div class="cnt-form-inputs">
            <div class="two-columns">
                <?php
                echo $this->Form->input(
                    'title',
                    array(
                        'required' => true,
                        'type' => 'text',
                        'id' => 'title',
                        'label' => __t('Communication.Title'),
                    )
                );
                ?>
            </div>
            <?php
            echo $this->Form->input(
                'subtitle',
                array(
                    'required' => true,
                    'type' => 'text',
                    'id' => 'subtitle',
                    'label' => __t('Communication.Subtitle'),
                )
            );
            ?>
            <div class="d-none">
                <?php
                echo $this->Form->input(
                    'communication_section_size',
                    array(
                        'type' => 'select',
                        'options' => isset($communications_sections_size) ? $communications_sections_size : null,
                        'id' => 'size_communication_section'
                    )
                );
                ?>
            </div>
            <?php
            echo $this->Form->input(
                'communication_section_id',
                array(
                    'label' => __t('Communication.Communication_section'),
                    'class' => 'select2-multiple select-subsection-js',
                    'type' => 'select',
                    'options' => $communication_sections,
                    'empty' => true,
                    'data-url' => Router::url(array(
                        'controller' => 'communications',
                        'action' => 'ajax_load_subsections',
                    )),
                    'data-div_subsection' => '#div_subsection',
                    'id' => 'select_communication_section_id'
                )
            );
            if ($action == ConstantsActionsNames::EDIT || $sections_subsections) {
            ?>
                <div id="div_subsection">
                    <?php
                    echo $this->Form->input(
                        'Communication.section_subsection_id',
                        array(
                            'label' => __t('Communication.Communication_subsection'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'empty' => true,
                            'id' => 'section_subsection_id',
                            'options' => $sections_subsections,
                            'data-url' => Router::url(array(
                                'controller' => 'communications',
                                'action' => 'ajax_load_communication_filters',
                            )),
                        )
                    );
                    ?>
                </div>
            <?php } else { ?>
                <div id="div_subsection">
                    <?php echo $this->element('../Communications/Elements/ajax_load_subsections'); ?>
                </div>
            <?php } ?>
            <div class="all-columns">
                <?php echo $this->Form->input(
                    'url',
                    array(
                        'required' => false,
                        'type' => 'text',
                        'id' => 'url',
                        'label' => __t('Communication.Url'),
                    )
                ); ?>
            </div>
            <?php
            echo $this->Form->input(
                'start_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js from-js',
                    'id' => 'start_date',
                    'data-to' => '#end_date',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Communication.Start_date'),
                )
            );
            echo $this->Form->input(
                'end_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js to-js',
                    'id' => 'end_date',
                    'data-from' => '#start_date',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Communication.End_date'),
                )
            );
            ?>
            <div class="all-columns">
                <label class="aag-subtitle" for="body"><?php echo __t('Communication.Body'); ?></label>
                <?php echo $this->Form->input(
                    'body',
                    array(
                        'label' => false,
                        'required' => true,
                        'type' => 'textarea',
                        'id' => 'body',
                    )
                ); ?>
            </div>
            <label class="center-check">
                <?php echo __t('Shortcut.Active'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('active', array('required' => true, 'type' => 'checkbox', 'id' => 'active', 'label' => false, 'div' => false)); ?>
                    <label for="active"></label>
                </div>
            </label>
            <label class="center-check">
                <?php echo __t('Communication.Popup'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('is_popup', array('required' => true, 'type' => 'checkbox', 'id' => 'is_popup', 'label' => false, 'div' => false)); ?>
                    <label for="is_popup"></label>
                </div>
            </label>
            <div id="cnt_popup" class="all-columns">
                <div class="cnt-form-inputs m-0-i">
                    <?php
                    echo $this->Form->input(
                        'start_date_popup',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'class' => 'fecha-js from-js',
                            'id' => 'start_date_popup',
                            'data-to' => '#end_date_popup',
                            'div' => array(
                                'class' => 'datepicker datepicker-label-block',
                            ),
                            'label' => __t('Communication.Start_date'),
                        )
                    );
                    echo $this->Form->input(
                        'end_date_popup',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'class' => 'fecha-js to-js',
                            'id' => 'end_date_popup',
                            'data-from' => '#start_date_popup',
                            'div' => array(
                                'class' => 'datepicker datepicker-label-block',
                            ),
                            'label' => __t('Communication.End_date'),
                        )
                    );
                    ?>
                </div>
            </div>
        </div>
        <div class="row p-top-1">
            <?php if (isset($communication) && !empty($communication['Communication'])) { ?>
                <div class="all-columns">
                    <label>
                        <?php echo __t('Shortcut.Actually_image'); ?>
                    </label>
                    <?php echo $this->Html->image(
                        FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication['Communication']['image']),
                        array(
                            'alt' => $communication['Communication']['title'],
                            'title' => $communication['Communication']['title'],
                            'class' => 'logotipo',
                            'style' => 'height: 50px; width: max-content; margin-top: 3px;',
                        )
                    ); ?>
                </div>
            <?php } ?>
            <div class="two-columns">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                <?php
                echo $this->Form->input(
                    'image',
                    array(
                        'id' => 'image-input',
                        'class' => 'dragdrop-js',
                        'label' => __t('General.Image'),
                        'type' => 'file',
                        'multiple' => false,
                        'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                        'div' => array(
                            'class' => 'field_file cont-fileWrapper',
                        ),
                    )
                );
                echo $this->Form->hidden('new_image', array('id' => 'new-image-input'));
                ?>
            </div>
            <div class="two-columns">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                <?php
                echo $this->Form->input(
                    'files.',
                    array(
                        'id' => 'files',
                        'class' => 'dragdrop-js dragdrop-multiple-js',
                        'label' => __t('General.Files'),
                        'type' => 'file',
                        'multiple' => true,
                        'accept' => '.' . ConstantsFileType::PDF,
                        'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                        'div' => array(
                            'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                        ),
                    )
                );
                ?>
            </div>
        </div>
        <div class="row p-horizontal-1">
            <div class="medium-12 columns p-normal">
                <div id="file-list-js">
                    <?php
                    if (isset($communication_files) && $communication_files) {
                        echo $this->element('../Communications/Elements/form_attached_files');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="p-top-1 <?php if (!isset($communication)) {
                        echo 'd-none';
                    } ?>" id='filters_communication'>
    <?php echo $this->element('../Communications/Elements/ajax_load_communication_filters'); ?>
</div>
</div>
<?php echo $this->Form->end(); ?>

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