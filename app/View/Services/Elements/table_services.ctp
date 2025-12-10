<?php echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach( $services as $service )
    {
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="service_<?php echo $service['Service']['id'] ?>"
			data-id="<?php echo $service['Service']['id'] ?>"
			data-url="
				<?php echo Router::url(array(
					'controller' => 'services',
					'action' => 'getDataService',
					$service['Service']['id'],
				)) ?>
			">
            <img title="<?php echo h($service['Service'][$selected_language]); ?>" src="<?php echo FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $service['Service']['url']); ?>"/>
            <div id="<?php echo 'language-' . $this->Session->read('Auth.User.language_code');?>">
                <?php echo h($service['Service'][$selected_language]); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'flex delete-service-js',
                        'data-confirmmsg' => __t('Maintenance.Service_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'services',
                            'action' => 'ajax_delete_service',
                            $service['Service']['id'],
                        )),
                        'data-id' => $service['Service']['id'],
                        'data-name' => $service['Service'][$selected_language],
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                );
            } ?>
        </div>
        <?php
    }
    ?>
</div>
