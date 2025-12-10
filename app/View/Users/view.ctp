<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('User.Users'),
                array(
                    'controller' => 'users',
                    'action' => 'listing'
                )
            ),
            __t('General.View'),
        ));?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium btn-back',
            )
        );?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo h($user['User']['name']).' '.h($user['User']['surname']); ?>
    </div>
    <div>
        <div>
            <div class="aag-subtitle p-top-1">
                <?php echo __t('User.User'); ?>
            </div>
            <div class="cnt-form-inputs">
                <div >
                    <strong style="color: black"><?php echo __t('User.Name') . ':';?></strong> <?php echo h($user['User']['name']);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('User.Surname') . ':';?></strong> <?php echo h($user['User']['surname']);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('User.User') . ':';?></strong> <?php echo h($user['User']['username']);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('User.Role') . ':';?></strong> <?php echo h($roles[$user['User']['role_id']]);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('User.Region') . ':';?></strong> <?php echo h($aag_region_name);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('Garage.Country') . ':';?></strong> <?php echo h($countries[$user['User']['country_id']] ?? __t('General.All'));?>
                </div>
                <?php if( $contact['Contact']['garage_id'] ){ ?>
                    <div >
                        <strong style="color: black"><?php echo __t('Contact.Garage') . ':';?></strong> <?php echo h($garage_name['Garage']['name']);?>
                    </div>
                <?php } ?>
                <?php if( $contact['Contact']['distributor_id'] ){ ?>
                    <div >
                        <strong style="color: black"><?php echo __t('Contact.Distributor') . ':';?></strong> <?php echo h($distributor_name['Distributor']['name']);?>
                    </div>
                <?php } ?>
                <div >
                    <strong style="color: black"><?php echo __t('User.Language') . ':';?></strong> <?php echo h($languages[$user['User']['language_id']]);?>
                </div>
                <div >
                    <strong style="color: black"><?php echo __t('User.Active') . ':';?></strong> <?php echo h(Booleano::toString($user['User']['active']));?>
                </div>
            </div>
            <div class="p-top-1">
                <?php if( !isset($user_image['UserImage']) ){?>
                    <img class="avatar" id ="img_view_user" src="<?php echo ConstantsPath::ADD_DEFAULT_IMAGE_BIG ?>"/>
                <?php
                } else {
                    $this->UserImage = ClassRegistry::init('UserImage');
                    $file = $this->UserImage->findById( $user_image['UserImage']['id'] );?>
                    <img class="avatar" id ="img_view_user" src="<?php echo FileManager::get_url(ConstantsPath::DIR_USER_IMAGES_CROP.'/'.$file['UserImage']['file']) ?>"/>
                <?php } ?>
            </div>
        </div>
        <div>
            <div class="aag-subtitle p-top-1">
                <?php echo __t('Contact.Contact'); ?>
            </div>
            <div class="cnt-form-inputs">
                <div>
                    <strong style="color: black"><?php echo __t('User.Name') . ':';?></strong> <?php echo h($user['User']['name']);?>
                </div>
                <div>
                    <strong style="color: black"><?php echo __t('User.Surname') . ':';?></strong> <?php echo h($user['User']['surname']);?>
                </div>
                <div>
                    <strong style="color: black"><?php echo __t('Contact.Phone') . ':';?></strong> <?php echo h($user['Contact']['phone']);?>
                </div>
                <div>
                    <strong style="color: black"><?php echo __t('Contact.Mobile_phone') . ':';?></strong> <?php echo h($user['Contact']['mobile_phone']);?>
                </div>
                <div class="medium-12 columns end">
                    <strong style="color: black"><?php echo __t('Contact.Email') . ':';?></strong> <?php echo h($user['Contact']['email']);?>
                </div>
                <div>
                    <strong style="color: black"><?php echo __t('Contact.Position') . ':';?></strong> <?php echo h($positions[$user['Contact']['position_id']]);?>
                </div>
                <div>
                    <strong style="color: black"><?php echo __t('User.Role') . ':';?></strong> <?php echo h($user['Role']['name'.__s()]);?>
                </div>
                <?php if( $contact['Contact']['garage_id'] ){ ?>
                    <div >
                        <strong style="color: black"><?php echo __t('Contact.Garage') . ':';?></strong> <?php echo h($garage_name['Garage']['name']);?>
                    </div>
                <?php } ?>
                <?php if( $contact['Contact']['distributor_id'] ){ ?>
                    <div >
                        <strong style="color: black"><?php echo __t('Contact.Distributor') . ':';?></strong> <?php echo h($distributor_name['Distributor']['name']);?>
                    </div>
                <?php } ?>
                <?php if( $contact['Contact']['logistic_center_id'] ){ ?>
                    <div >
                        <strong style="color: black"><?php echo __t('Contact.Logistic_center') . ':';?></strong> <?php echo h($logistic_name['LogisticCenter']['name'.__s()]);?>
                    </div>
                <?php } ?>
                <div >
                    <strong style="color: black"><?php echo __t('Contact.Identification_number') . ':';?></strong> <?php echo h($user['Contact']['identification_number']);?>
                </div>
            </div>
            <?php if( $contact_regions ){ ?>
                <div >
                    <strong style="color: black"><?php echo __t('General.Regions') . ':';?></strong>
                    <?php foreach( $contact_regions as $region ){ ?>
                        <?php echo h($regions[$region['ContactRegion']['region_id']]);?>
                    <?php }?>
                </div>
            <?php } ?>
            <div >
                <?php if(!empty($contact['garage_networks'] )){ ?>
                    <strong style="color: black"><?php echo __t('Garage.Garage') . ' ' . __t('Network.Networks');?></strong>
                    <br>
                <?php } ?>
                <?php foreach($contact['garage_networks'] as $garage_network){
                    echo $this->Html->image(FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $networks_images[$garage_network['NetworkContactBdm']    ['network_id']]), array('class' => 'logotipo', 'style' => 'margin:.5em !important;'));
                } ?>
            </div>
            <div >
                <?php if(!empty($contact['distributor_networks'] )){ ?>
                    <strong style="color: black"><?php echo __t('Distributor.Distributor') . ' ' . __t('Network.Networks');?></strong>
                    <br>
                <?php } ?>
                <?php foreach($contact['distributor_networks'] as $distributor_network){
                    echo $this->Html->image(FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_networks_images[$distributor_network    ['DistributorNetworkContactBdm']['distributor_network_id']]), array('class' => 'logotipo', 'style' => 'margin:.5em !important;'));
                } ?>
            </div>
        </div>
    </div>
</div>