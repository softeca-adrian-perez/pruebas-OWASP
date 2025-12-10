<?php
$action = $this->request->action;
echo $this->Html->script('subcategories.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v='.Configure::read('VERSION_CACHE'));
echo $this->Form->create(
    'SectionSubsection',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
    echo $this->Form->hidden('SectionSubsection.id');
    ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            if($action == 'add_subsection')
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                        __t('Maintenance.Maintenance'),
                        array(
                            'controller' => 'maintenance',
                            'action' => 'home'
                        )
                    ),
                    $this->Html->link(
                        __t('Communication.Communications') .' '.__t('Communication.Communications_subsections'),
                        array(
                            'controller' => 'communications',
                            'action' => 'maintenance_section_subsection'
                        )
                    ),
                    __t('General.Add'),
                ));
            }
            else
            {
                echo $this->Html->breadcrumb(array(
                    $this->Html->link(
                        __t('Maintenance.Maintenance'),
                        array(
                            'controller' => 'maintenance',
                            'action' => 'home'
                        )
                    ),
                    $this->Html->link(
                        __t('Communication.Communications') .' '.__t('Communication.Communications_subsections'),
                        array(
                            'controller' => 'communications',
                            'action' => 'maintenance_section_subsection'
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
        <div class="aag-title m-bottom-1">
            <?php echo __t('Communication.Communications_subsections'); ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'name_en',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Communication.English_name'),
                )
            );
            echo $this->Form->input(
                'name_fr',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Communication.French_name'),
                )
            );
            echo $this->Form->input(
                'name_de',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Communication.German_name'),
                )
            );
            echo $this->Form->input(
                'communication_section_id',
                array(
                    'label' => __t('Communication.Communication_section'),
                    'required' => true,
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'empty' => true,
                    'options' => $sections,
                    'id' => 'section',
                    'data-url' => Router::url(array(
                        'controller' => 'communications',
                        'action' => 'ajax_load_subsection_filters',
                    )),
                )
            );
            ?>
        </div>
        <div class="aag-subtitle m-top-1">
            <?php echo __t('General.Image'); ?>
        </div>
        <div class="m-0-i" id="cnt-img">
            <?php
            echo $this->Form->input(
                'image',
                array(
                    'id' => 'image-input',
                    'class' => 'dragdrop-js',
                    'label' => false,
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
        <?php
        if(isset($communication_subsection) && !empty($communication_subsection['SectionSubsection']['image']))
        {
            ?>
            <div class="aag-subtitle">
                <?php echo __t('Section.Actually_image'); ?>
            </div>
            <?php
            echo $this->Html->image(
                FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication_subsection['SectionSubsection']['image']),
                array(
                    'alt' => $communication_subsection['SectionSubsection']['name'.__s()],
                    'title' => $communication_subsection['SectionSubsection']['name'.__s()],
                    'class' => 'logotipo',
                    'style' => 'height: 50px; width: max-content;',
                )
            );
        }
        ?>
        <div class="<?php if(!isset($section_subsection)) { echo 'd-none'; } ?>" id='filters_subsection'>
            <?php echo $this->element('../Communications/Elements/ajax_load_subsection_filters'); ?>
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