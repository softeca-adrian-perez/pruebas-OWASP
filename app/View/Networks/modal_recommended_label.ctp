<div class="modal-recommended-label">
    <h1>
        <?php echo __t('NetworkRecommended.Edit_recommended_network'); ?>
        <span class="close-js">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                <path fill="none" stroke="#a8a8a8" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144M368 144L144 368"/>
            </svg>
        </span>
    </h1>
    <?php
    echo $this->Form->create(
        'NetworkRecommended', array(
            'url' => [
                'controller' => 'networks',
                'action' => 'recommended_networks',
                $network_id
            ]
        )
    );
        echo $this->Form->hidden('NetworkRecommended.network_id', ['value' => $network_id]);
        echo $this->Form->hidden('NetworkRecommended.network_recommended_id', ['value' => $network_recommended['NetworkRecommended']['internal_network_id'] ?? null]);
        ?>
        <label class="center-check p-top-1">
            <?php echo __t('NetworkRecommended.Recommended_label'); ?>
            <div class="aag-switch round small">
                <?php
                    $isChecked = isset($network_recommended['NetworkRecommended']['recommended_label']) && $network_recommended['NetworkRecommended']['recommended_label'] == 1;
                    echo $this->Form->input('NetworkRecommended.recommended_label', array(
                        'id' => 'recommended',
                        'label' => false,
                        'div' => false,
                        'type' => 'checkbox',
                        'checked' => $isChecked
                    ));
                ?>
                <label for="recommended"></label>
            </div>
        </label>
        <br />
        <div class="aag-title clear">
            <?php echo __t('NetworkRecommended.Image_in_garage_page'); ?>
        </div>
        <?php
        if(isset($network_recommended['NetworkRecommended']['image_recommended']))
        {
            ?>
            <br />
            <div class="clear-column">
                <img
                    class="trading_image img_table"
                    src="<?php echo FileManager::get_url(FilePaths::NETWORKS_RECOMMENDED_IMAGES . $network_recommended['NetworkRecommended']['image_recommended']); ?>"
                />
            </div>
            <span class="aag-icon-papelera c-fallo delete-image-js" style="cursor: pointer;"
                data-delete-url="
                <?php echo Router::url(
                    array(
                        'controller' => 'networks',
                        'action' => 'ajax_delete_recommended_image',
                        $network_recommended['NetworkRecommended']['id'],
                    )
                ); ?>"
                data-recommended-id="<?php echo $network_recommended['NetworkRecommended']['id']; ?>">
            </span>
            <?php
        }
        ?>
        <br />
        <div class="two-columns">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
                echo $this->Form->input(
                    'NetworkRecommended.image_recommended',
                    array(
                        'id' => 'service-file-add',
                        'class' => 'dragdrop-js',
                        'label' => false,
                        'type' => 'file',
                        'multiple' => false,
                        'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                        'after' => '<div class="ta-center">' . __t('Network.Img_extension') . '</div>',
                        'div' => [
                            'class' => 'field_file cont-fileWrapper',
                        ],
                    )
                );
                echo $this->Form->hidden(
                    'new_image_recommended',
                    array(
                        'id' => 'new-image-input',
                    )
                );
            ?>
        </div>
        <br />
        <div class="aag-title clear">
            <?php echo __t('NetworkRecommended.Image_in_garages_list'); ?>
        </div>
        <?php
        if(isset($network_recommended['NetworkRecommended']['image_recommended_list']))
        {
            ?>
            <br />
            <div class="clear-column">
                <img
                    class="trading_image img_table"
                    src="<?php echo FileManager::get_url(FilePaths::NETWORKS_RECOMMENDED_IMAGES . $network_recommended['NetworkRecommended']['image_recommended_list']); ?>"
                />
            </div>
            <span class="aag-icon-papelera c-fallo delete-image-js" style="cursor: pointer;"
                data-delete-url="
                <?php echo Router::url(
                    array(
                        'controller' => 'networks',
                        'action' => 'ajax_delete_recommended_image_list',
                        $network_recommended['NetworkRecommended']['id'],
                    )
                ); ?>"
                data-recommended-id="<?php echo $network_recommended['NetworkRecommended']['id']; ?>">
            </span>
            <?php
        }
        ?>
        <br />
        <div class="two-columns">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
                echo $this->Form->input(
                    'NetworkRecommended.image_recommended_list',
                    array(
                        'id' => 'service-file-list-add',
                        'class' => 'dragdrop-js',
                        'label' => false,
                        'type' => 'file',
                        'multiple' => false,
                        'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                        'after' => '<div class="ta-center">' . __t('Network.Img_extension') . '</div>',
                        'div' => [
                            'class' => 'field_file cont-fileWrapper',
                        ],
                    )
                );
                echo $this->Form->hidden(
                    'new_image_recommended_list',
                    array(
                        'id' => 'new-image-list-input',
                    )
                );
            ?>
        </div>
        <br />
        <div class="ta-center">
            <?php echo $this->Form->submit(__t('General.Save'), array('div' => false, 'class' => 'aag-button large green','id' => 'btn-guardar')); ?>
        </div>
    <?php echo $this->Form->end(); ?>
</div>