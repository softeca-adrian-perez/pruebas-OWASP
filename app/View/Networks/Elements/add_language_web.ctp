<div class="cnt-data aag-padding">
	<div class="aag-title p-bottom-1">
		<?php echo __t('Language.Select_language'); ?>
	</div>
		<?php
		echo $this->Form->create(
			'LanguageNetworkWeb',
			array(
				'id' => 'form' . (isset($language_web_network) ? '-' . $language_web_network['LanguageWebNetwork']['id'] : ''),
				'class' => 'form-horizontal',
				'enctype' => 'multipart/form-data'
			)
		);
		?>
		<div class="cnt-form-inputs">
			<?php
			echo $this->Form->hidden(
				'LanguageNetworkWeb.id',
				array(
					'class' => 'language-id' . (isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '') . '-js',
					'value' => isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : null,
				)
			);
			echo $this->Form->hidden(
				'LanguageNetworkWeb.network_id',
				array(
					'class' => 'language-network_id' . (isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '') . '-js',
					'value' => $network_id
				)
			);
			?>
			<div class="required">
				<?php
				echo $this->Form->input(
					'LanguageNetworkWeb.name_loco',
					array(
						'label' => __t('Language.Name'),
						'type' => 'text',
						'empty' => true,
						'class' => 'language-name_loco' . (isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '') . '-js',
						'value' => isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['name'] : null,
						'required' => true,
					)
				);
				?>
			</div>
			<div class="required">
				<?php
				echo $this->Form->input(
					'LanguageNetworkWeb.code_loco',
					array(
						'label' => __t('Language.Code'),
						'type' => 'text',
						'empty' => true,
						'placeholder' => __t('Language.Code_placeholder'),
						'class' => 'language-code_loco' . (isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '') . '-js code-loco',
						'value' => isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['code'] : null,
						'required' => true,
						'disabled' => isset($language_web_network) ? true : false
					)
				);
				?>
			</div>
			<div class="required">
				<?php
				echo $this->Form->input(
					'LanguageNetworkWeb.flag',
					array(
						'label' => __t('Language.Flag'),
						'type' => 'select',
						'options' => $languages_webs_flags,
						'empty' => true,
						'data-url_flags' => FilePaths::LANGUAGES_WEBS_FLAGS_IMAGES_RELATIVE,
						'class' => 'flag_loco-js language-flag_loco' . (isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '') . '-js',
						'selected' => isset($language_web_network) ? $language_web_network['LanguageWebFlag']['url'] : null,
						'required' => true,
					)
				);
				?>
			</div>
		</div>
		<div class="clear ta-center m-top-1">
			<?php
			echo $this->Html->Link(
				__t('General.Save'),
				array(),
				array(
					'escape' => false,
					'title' => __t('General.Save'),
					'class' => 'aag-button medium green save_language_network-js',
					'data-element_id' => isset($language_web_network) ? $language_web_network['LanguageWebNetwork']['id'] : '',
					'data-url' => Router::url(
						array(
							'controller' => 'networks',
							'action' => 'ajax_add_edit_language_web',
						)
					)
				)
			);
			?>
		</div>
	<?php echo $this->Form->end(); ?>
</div>

