<?php
echo $this->Html->script('genarts_families.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('languages_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>

<?php echo $this->Form->create(
	'Network',
	[
		'url' => [
			'controller' => 'networks',
			'action' => 'general_settings',
			$network_id
		],
		'enctype' => 'multipart/form-data'
	]
);
?>

<div class="cnt-breadcrumb">
	<div>
		<?php
		echo $this->Html->breadcrumb(array(
			$this->Html->link(
				__t('Network.Networks'),
				array(
					'controller' => 'networks',
					'action' => 'families_configuration',
					$network_id
				)
			),
			__t('Configuration.Configuration')
		));
		?>
	</div>
	<div>
		<?php echo $this->element('Comun/form_actions', $cancel_action); ?>
	</div>
</div>

<?php echo $this->element('../Networks/configuration_tabs', array('selected' => 'general_settings')); ?>

<div class="cnt-data aag-padding">
	<div class="p-top-1">
		<div>
			<div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="1">
				<?php echo __t('General.Search_radius'); ?>
				<i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
			</div>
			<div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="1">
				<div class="cnt-form-inputs m-top-1 required">
					<?php
					echo $this->Form->input(
						'default_mileage',
						array(
							'type' => 'float',
							'required' => true,
							'label' => __t('Network.Default_search_radius'),
							'min' => 1
						)
					);
					echo $this->Form->input(
						'distance_unit_id',
						array(
							'type' => 'text',
							'required' => true,
							'value' => $distance_unit_name,
							'label' => __t('Distance.Unit'),
							'disabled' => true
						)
					);
					?>
				</div>
			</div>
		</div>
		<div class="p-top-1 p-bottom-1">
			<div class=" toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="2">
				<?php echo __t('General.Language_administration'); ?>
				<i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
			</div>
			<div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="2">
				<div class="ta-right">
					<div>
						<?php
						echo $this->Form->button(
							__t('Language.New_language'),
							array(
								'escape' => false,
								'id' => 'modal-add-language-web',
								'data-open' => "addLanguageWeb",
								'type' => 'button',
								'title' => __t('Language.New_language'),
								'class' => 'aag-button medium green modal_language_webs-js',
							)
						);
						?>
					</div>
				</div>
				<div class="o-auto">
					<table id="languages_table" class="table-tracking not-change-background-color">
						<thead>
							<tr>
								<th><?php echo __t('General.Active'); ?></th>
								<th><?php echo __t('General.Default'); ?></th>
								<th><?php echo __t('User.Language'); ?></th>
								<th></th>
								<th width="1"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($languages_webs_network as $language_web_network) { ?>
								<tr>
									<td>
										<?php
										echo $this->Form->input(
											'',
											array(
												'type' => 'checkbox',
												'checked' => $language_web_network['LanguageWebNetwork']['active'],
												'name' => 'language_web_network_active',
												'class' => 'language_web_network_action-js',
												'disabled' => $language_web_network['LanguageWebNetwork']['is_default'],
												'data-url' => Router::url(array(
													'controller' => 'networks',
													'action' => 'ajax_active_language_web',
													$language_web_network['LanguageWebNetwork']['id'],
												)),
												'data-url_redirect' => Router::url(array(
													'controller' => 'networks',
													'action' => 'general_settings',
													$network_id
												)),
												'data-confirmmsg' => __t('Language.Active?'),
												'data-msg_correct' => __t('Language.Correct_saved'),
												'data-msg_bad' => __t('Constants.Message_bad_saved'),
											)
										);
										?>
									</td>
									<td class="color-blue-text">
										<?php
										echo $this->Form->input(
											'',
											array(
												'type' => 'radio',
												'name' => 'language_web_network_default',
												'options' => array('0' => ''),
												'checked' => $language_web_network['LanguageWebNetwork']['is_default'],
												'disabled' => true
											)
										);
										?>
									</td>
									<td class="color-blue-text">
										<?php
										echo $this->Html->image(
											FilePaths::LANGUAGES_WEBS_FLAGS_IMAGES_RELATIVE . $language_web_network['LanguageWebFlag']['url'],
											array(
												'alt' => $language_web_network['LanguageWebNetwork']['name'],
												'title' => $language_web_network['LanguageWebNetwork']['name'],
												'width' => 23
											)
										) . ' ' . h($language_web_network['LanguageWebNetwork']['name'])
											. ' (' . h($language_web_network['LanguageWebNetwork']['code']) . ')';
										?>
									</td>
									<td class="ta-right">
										<?php
										echo $this->Form->button(
											__t('Language.Update_translations') .
												'<svg width="10" height="10" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
												<path fill="#fff" d="M102.59,341.42a15,15,0,0,1-13.42-8.28,187.41,187.41,0,0,1,35.11-216.86c73.18-73.19,192.26-73.19,265.44,0a15,15,0,0,1-21.21,21.21C307,76,207,76,145.49,137.49A157.45,157.45,0,0,0,116,319.69a15,15,0,0,1-13.4,21.73Z"/>
												<path fill="#fff" d="M257,436.61a187.1,187.1,0,0,1-132.72-54.88,15,15,0,1,1,21.21-21.22C207,422,307,422,368.51,360.51A157.45,157.45,0,0,0,398,178.31a15,15,0,0,1,26.82-13.45A187.62,187.62,0,0,1,257,436.61Z"/>
												<path fill="#fff" d="M315.21,148.25a15,15,0,0,1-1.47-29.92l47.43-4.73-9.51-47.67a15,15,0,0,1,29.42-5.86L393.82,124a15,15,0,0,1-13.22,17.86l-63.88,6.37C316.21,148.23,315.71,148.25,315.21,148.25Z"/>
												<path fill="#fff" d="M147.61,450a15,15,0,0,1-14.7-12.07l-12.74-63.88a15,15,0,0,1,13.23-17.86l63.88-6.37a15,15,0,0,1,3,29.85l-47.43,4.73,9.5,47.67A15,15,0,0,1,147.61,450Z"/>
											</svg>',
											array(
												'type' => 'button',
												'class' => 'aag-button small two m-0 language_web_network_action-js',
												'data-url' => Router::url(array(
													'controller' => 'networks',
													'action' => 'updateLocoTranslations',
													$network_id,
													$language_web_network['LanguageWebNetwork']['code'],
												)),
												'data-url_redirect' => Router::url(array(
													'controller' => 'networks',
													'action' => 'general_settings',
													$network_id
												)),
												'data-confirmmsg' => __t('Language.Update_translations?'),
												'data-msg_correct' => __t('Language.Correct_translated'),
												'data-msg_bad' => __t('Constants.Message_bad_saved'),
											)
										);
										?>
									</td>
									<td class="td-icons td-icons--right">
										<span class="bt-content">
											<div style="display: none;" id="editLanguageWeb-<?php echo $language_web_network['LanguageWebNetwork']['id']; ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
												<div id="modal-edit-language-web-<?php echo $language_web_network['LanguageWebNetwork']['id']; ?>" class="medium-12 columns p-right-0">
													<?php echo $this->element('../Networks/Elements/add_language_web', array('language_web_network' => $language_web_network)); ?>
												</div>
												<a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
											</div>
											<?php
											if (!$language_web_network['LanguageWebNetwork']['is_default']) {
												echo $this->Html->Link(
													'<span class="cursor-pointer aag-icon-papelera c-fallo"></span>',
													array(),
													array(
														'escape' => false,
														'title' => __t('General.Delete'),
														'class' => 'delete-js',
														'data-url' => Router::url(array(
															'controller' => 'networks',
															'action' => 'ajax_delete_language_web',
															$network_id,
															$language_web_network['LanguageWebNetwork']['id'],
														)),
														'data-url_redirect' => Router::url(array(
															'controller' => 'networks',
															'action' => 'general_settings',
															$network_id
														)),
														'data-confirmmsg' => __t('Language.Delete?'),
														'data-msg_correct' => __t('Language.Correct_deleted'),
														'data-msg_bad' => __t('Constants.Message_bad_deleted'),
														'style' => 'display: flex;'
													)
												);
											}
											echo $this->Form->button(
												'<span class="cursor-pointer edit-provider aag-icon-editar" style="color: var(--primary-color)"></span>',
												array(
													'id' => 'modal-edit-language-web-' .  $language_web_network['LanguageWebNetwork']['id'],
													'data-open' => 'editLanguageWeb-' .  $language_web_network['LanguageWebNetwork']['id'],
													'escape' => false,
													'type' => 'button',
													'class' => 'clean-button modal_language_webs-js',
												)
											);
											?>
										</span>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<br />
				<?php if (isset($languages_webs_network) && !empty($languages_webs_network)) { ?>
					<div class="ta-right">
						<?php
						echo $this->Form->button(
							__t('Language.Update_all_translations') .
								'<svg width="10" height="10" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
								<path style="fill: var(--secondary-color) !important;" d="M102.59,341.42a15,15,0,0,1-13.42-8.28,187.41,187.41,0,0,1,35.11-216.86c73.18-73.19,192.26-73.19,265.44,0a15,15,0,0,1-21.21,21.21C307,76,207,76,145.49,137.49A157.45,157.45,0,0,0,116,319.69a15,15,0,0,1-13.4,21.73Z"/>
								<path style="fill: var(--secondary-color) !important;" d="M257,436.61a187.1,187.1,0,0,1-132.72-54.88,15,15,0,1,1,21.21-21.22C207,422,307,422,368.51,360.51A157.45,157.45,0,0,0,398,178.31a15,15,0,0,1,26.82-13.45A187.62,187.62,0,0,1,257,436.61Z"/>
								<path style="fill: var(--secondary-color) !important;" d="M315.21,148.25a15,15,0,0,1-1.47-29.92l47.43-4.73-9.51-47.67a15,15,0,0,1,29.42-5.86L393.82,124a15,15,0,0,1-13.22,17.86l-63.88,6.37C316.21,148.23,315.71,148.25,315.21,148.25Z"/>
								<path style="fill: var(--secondary-color) !important;" d="M147.61,450a15,15,0,0,1-14.7-12.07l-12.74-63.88a15,15,0,0,1,13.23-17.86l63.88-6.37a15,15,0,0,1,3,29.85l-47.43,4.73,9.5,47.67A15,15,0,0,1,147.61,450Z"/>
							</svg>',
							array(
								'type' => 'button',
								'class' => 'aag-button two small outlined language_web_network_action-js',
								'style' => 'background: none;',
								'data-url' => Router::url(array(
									'controller' => 'networks',
									'action' => 'updateLocoTranslations',
									$network_id,
								)),
								'data-url_redirect' => Router::url(array(
									'controller' => 'networks',
									'action' => 'general_settings',
									$network_id
								)),
								'data-confirmmsg' => __t('Language.Update_all_translations?'),
								'data-msg_correct' => __t('Language.Correct_translated'),
								'data-msg_bad' => __t('Constants.Message_bad_saved'),
							)
						);
						?>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="p-top-1 p-bottom-1">
			<div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="3">
				<?php echo __t('General.Notifications'); ?>
				<i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
			</div>
			<div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="3">
				<div class="cnt-form-inputs m-top-1">
					<?php
					echo $this->Form->input(
						'Network.email_booking_contact_id',
						array(
							'label' => __t('Email.Booking'),
							'class' => 'clear_field dynamicSelect2_contacts_emails contact-id-js',
							'type' => 'select',
							'multiple' => false,
							'data-selected_contacts_emails' => isset($email_booking_contact_id) ? $email_booking_contact_id : array(),
							'empty' => true,
						)
					);
					echo $this->Form->input(
						'Network.email_enquiry_contact_id',
						array(
							'label' => __t('Email.Enquiry'),
							'class' => 'clear_field dynamicSelect2_contacts_emails contact-id-js',
							'type' => 'select',
							'multiple' => false,
							'data-selected_contacts_emails' => isset($email_enquiry_contact_id) ? $email_enquiry_contact_id : array(),
							'empty' => true,
						)
					);
					?>
				</div>
			</div>
		</div>
		<div class="p-top-1 p-bottom-1">
			<div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="4">
				<?php echo __t('Network.Hide_phone_numbers_in_pws'); ?>
				<i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
			</div>
			<div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="4">
				<label class="center-check">
					<div class="aag-switch round small">
						<input id="hide_phone_numbers_in_pws" name="hide_phone_numbers_in_pws" type="checkbox" class="presets"
							<?php
							if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
								echo ' disabled ';
							}
							echo !empty($hide_phone_numbers_in_pws) ? 'checked' : ''
							?> />
						<label for="hide_phone_numbers_in_pws"></label>
					</div>
				</label>
			</div>
		</div>
	</div>
</div>
<div style="display: none;" id="addLanguageWeb" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<div id="modal-add-language-web" class="medium-12 columns p-right-0">
		<?php echo $this->element('../Networks/Elements/add_language_web'); ?>
	</div>
	<a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<?php echo $this->Form->end(); ?>
