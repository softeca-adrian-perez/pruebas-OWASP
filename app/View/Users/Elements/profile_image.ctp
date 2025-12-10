<?php echo $this->Form->hidden('User.image'); ?>

<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true"
     role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<?php
echo $this->Form->hidden('User.new_profile_image', array('id' => 'new-image-input'));
if(empty($principal_image)){
    if($img_url == ConstantsPath::DIR_USER_IMAGES_CROP . '/') {
        $img_url = ConstantsPath::ADD_IMAGE_IMAGE;
    }
    $label = '<img id="image" class="profile-image" src="'. $img_url .'">';
}
else{
    if($principal_image['UserImage']['file'] == ''){
        $label = '<img id="image" class="profile-image" src="'. ConstantsPath::ADD_IMAGE_IMAGE . '">';
    }else{
        $label = '<img id="image" class="profile-image" src="'. FileManager::get_url(ConstantsPath::DIR_USER_IMAGES_CROP . '/' . $principal_image['UserImage']['file']) .'" style="height:225px !important;">';
    }
}
echo $this->Form->input(
    'User.image-change',
    array(
        'type' => 'file',
        'id' => 'image-change',
        'hidden' => true,
        'label' => $label
    )
);
echo $this->Form->input(
    'User.image-input',
    array(
        'type' => 'file',
        'id' => 'image-input',
        'hidden' => true,
        'label' => false,
    )
)
?>
