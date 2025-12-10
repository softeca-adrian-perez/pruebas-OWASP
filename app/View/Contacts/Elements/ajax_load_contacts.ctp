<div class="aag-subtitle">
    <?php
    if(isset($position)) { echo $position['Position']['name'.__s()]; }
    else { echo __t('Contact.All_contacts'); }
    ?>
</div>
    <?php foreach( $contacts as $contact){ ?>
        <div class="contact-directory">
            <div>
                <?php
                if(!$contact['UserImage']['id'])
                {
                    ?> <img class="avatar" id ="img_view_user" src="<?php echo ConstantsPath::ADD_DEFAULT_IMAGE_BIG ?>"/> <?php
                }
                else
                {
                    $this->UserImage = ClassRegistry::init('UserImage');
                    $file = $this->UserImage->findById( $contact['UserImage']['id'] );?>
                    <img class="avatar" id ="img_view_user" src="<?php echo FileManager::get_url(ConstantsPath::DIR_USER_IMAGES_CROP.'/'.$contact['UserImage']['file']) ?>"/>
                    <?php
                }
                ?>
            </div>
            <div>
                <div class="fw-bold">
                    <?php echo $contact['Contact']['full_name']; ?>
                </div>
                <div>
                    <?php echo $positions[$contact['Contact']['position_id']]; ?>
                </div>
                <div>
                    <?php echo $this->Html->link( h($contact['Contact']['email']), 'mailto:'. h($contact['Contact']['email'])); ?>
                </div>
                <div>
                    <?php
                    if($contact['Contact']['phone'] ) { echo __t('Contact.Phone').': '.$contact['Contact']['phone']; }
                    if($contact['Contact']['mobile_phone'])
                    {
                        if($contact['Contact']['phone']) { echo ' / '; }
                        echo __t('Contact.Mobile_phone').': '.$contact['Contact']['mobile_phone'];
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
