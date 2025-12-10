<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('suppliers.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('suppliers_maintenance.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
$action = $this->request->action;
echo $this->Form->create(
    'Supplier',
    array(
        'id' => 'form_suppliers-js',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);

echo $this->Form->hidden('Supplier.id');
echo $this->Form->hidden('SupplierImage.id');
echo $this->Form->hidden('SupplierFile.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Suppliers.Suppliers'),
                    array(
                        'controller' => 'suppliers',
                        'action' => 'maintenance_suppliers'
                    )
                ),
                __t('Suppliers.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Suppliers.Suppliers'),
                    array(
                        'controller' => 'suppliers',
                        'action' => 'maintenance_suppliers'
                    )
                ),
                __t('Suppliers.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions'); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Suppliers.Add');
        } else {
            echo __t('Suppliers.Edit');
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'name',
                'label' => __t('Suppliers.Name'),
            )
        );
        echo $this->Form->input(
            'code',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'name',
                'label' => __t('Suppliers.Code'),
            )
        );
        echo $this->Form->input(
            'VAT_number',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.VAT_number'),
            )
        );
        echo $this->Form->input(
            'siret',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.Siret'),
            )
        );
        echo $this->Form->input(
            'phone',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.Phone'),
            )
        );
        echo $this->Form->input(
            'fax',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.Fax'),
            )
        );
        echo $this->Form->input(
            'email',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.Email'),
            )
        );
        echo $this->Form->input(
            'address1',
            array(
                'type' => 'text',
                'required' => true,
                'id' => 'autocomplete-address',
                'placeholder' => '',
                'data-map' => 'map-event-location',
                'label' => __t('Suppliers.Address1'),
                'class' => 'input-disabled-address1',
                'data-key' => Texto::encryptDecryptText(GOOGLE_API_KEY, false),
                'data-user_aag_region_id' => $user_aag_region_id,
            )
        );
        echo $this->Form->input(
            'address2',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Suppliers.Address2'),
                'class' => 'input-disabled',
                'id' => 'autocomplete-address2',
            )
        );
        echo $this->Form->input(
            'postcode',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'autocomplete-postcode',
                'label' => __t('Suppliers.Postcode'),
            )
        );
        echo $this->Form->input(
            'town',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'autocomplete-town',
                'label' => __t('Suppliers.Town'),
            )
        );
        echo $this->Form->input(
            'web',
            array(
                'required' => true,
                'type' => 'text',
                'id' => 'web',
                'label' => __t('Suppliers.Web'),
            )
        );
        echo $this->Form->input(
            'url_channel',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'url_channel',
                'label' => __t('Suppliers.Youtube_channel'),
            )
        );
        echo $this->Form->input(
            'url_video',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'url_video',
                'label' => __t('Suppliers.Youtube_url'),
            )
        );
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'required' => true,
                'options' => $aag_regions,
                'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                'disabled' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
                'value' => isset($supplier) ? $supplier['Supplier']['aag_region_id'] : $user_aag_region_id,
                'id' => 'aag-region-select',
            )
        );
        ?>
    </div>

    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'description',
            array(
                'label' => __t('Suppliers.Description'),
                'required' => false,
                'type' => 'textarea',
                'id' => 'description',
            )
        );
        ?>
        <label class="center-check p-top-1">
            <?php echo __t('Suppliers.Active'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('active', array('id' => 'activeInput-2', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                <label for="activeInput-2"></label>
            </div>
        </label>
    </div>

    <div class="aag-subtitle">
        <?php echo __t('Suppliers.Networks'); ?>
    </div>
    <div class="cnt-form-inputs cont-services">
        <?php
        foreach ($networks as $key => $network) {
        ?>
            <div class="brands_checkbox" data-id="<?php echo $key ?>">
                <label class="jc-center m-0">
                    <?php
                    $valor = false;
                    $assigned = $network['Network']['id'];
                    if (isset($supplier_networks)) {
                        foreach ($supplier_networks as $supplier_network) {
                            $compared = $supplier_network['SupplierNetwork']['network_id'];
                            if ($assigned == $compared) {
                                $valor = true;
                            }
                        }
                    }
                    if (isset($this->request->data['SupplierNetwork']['network_id'])) {
                        if ($this->request->data['SupplierNetwork']['network_id'][$key] != ConstantsBooleans::NO) {
                            $valor = true;
                        }
                    }
                    echo $this->Form->input(
                        'SupplierNetwork.network_id.' . $key,
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'data-id' => $key,
                            'value' => $network['Network']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable">
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']),
                            array(
                                'title' => $network['Network']['name'],
                                'alt' => $network['Network']['name']
                            )
                        );
                        ?>
                    </span>
                </label>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Suppliers.Trading_groups'); ?>
    </div>
    <div class="cnt-form-inputs cont-services">
        <?php foreach ($trading_groups as $key => $trading_group) { ?>
            <div class="brands_checkbox" data-id="<?php echo $key ?>">
                <label class="jc-center m-0">
                    <?php
                    $valor = false;
                    $assigned = $trading_group['TradingGroup']['id'];
                    if (isset($supplier_trading_groups)) {
                        foreach ($supplier_trading_groups as $supplier_trading_group) {
                            $compared = $supplier_trading_group['SupplierTradingGroup']['trading_group_id'];
                            if ($assigned == $compared) {
                                $valor = true;
                            }
                        }
                    }
                    if (isset($this->request->data['SupplierTradingGroup']['trading_group_id'])) {
                        if ($this->request->data['SupplierTradingGroup']['trading_group_id'][$key] != ConstantsBooleans::NO) {
                            $valor = true;
                        }
                    }
                    echo $this->Form->input(
                        'SupplierTradingGroup.trading_group_id.' . $key,
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'data-id' => $key,
                            'value' => $trading_group['TradingGroup']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable p-bottom-1">
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                            array(
                                'title' => $trading_group['TradingGroup']['name'],
                                'alt' => $trading_group['TradingGroup']['name']
                            )
                        );
                        ?>
                    </span>
                </label>
            </div>
        <?php } ?>
    </div>

    <?php
    echo $this->Form->input(
        'publication_date',
        array(
            'type' => 'text',
            'required' => true,
            'class' => 'fecha-js',
            'id' => 'publication-date',
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
            'label' => __t('Suppliers.Publication_date'),
        )
    );
    ?>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Suppliers.Image'); ?>*
    </div>
    <span class="fs-small"><?php echo __t('Suppliers.Supported_formats') ?> (png, jpg)</span>
    <?php
    echo $this->Form->input(
        'SupplierImage.file',
        array(
            'id' => 'image-input',
            'class' => 'dragdrop-js',
            'label' => false,
            'type' => 'file',
            'multiple' => false,
            'before' => '<div class="text-drop">' . __t('Suppliers.Image') . '</div>',
            'div' => array(
                'class' => 'field_file cont-fileWrapper',
            ),
            'required' => true
        )
    );
    echo $this->Form->hidden(
        'SupplierImage.new_image',
        array(
            'id' => 'new-image-input',
        )
    );
    if (!empty($supplier['SupplierImage']['file'])) {
    ?>
        <div id="currentTitle">
            <?php echo __t('Shortcut.Actually_image'); ?>
        </div>
        <?php
        echo $this->Html->image(
            FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']),
            array(
                'alt' => $supplier['SupplierImage']['file'],
                'title' => $supplier['SupplierImage']['file'],
                'class' => 'logotipo',
                'style' => 'height:50px',
            )
        );
        ?>
    <?php } ?>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Suppliers.Files'); ?>
    </div>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
    <?php
    echo $this->Form->input(
        'SupplierFile.file.',
        array(
            'id' => 'suppliers-files',
            'class' => 'dragdrop_suppliers-js dragdrop_suppliers-multiple-js',
            'label' => false,
            'type' => 'file',
            'before' => '<div class="text-drop">' . __t('Suppliers.Drop_files') . '</div>',
            'data-suppliers_categories' => json_encode($suppliers_categories),
            'data-active_options' => json_encode(array(0 => __t('General.No'), 1 => __t('General.Yes'))),
            'data-get_category_name_by_id' => Router::url(array(
                'controller' => 'suppliers',
                'action' => 'ajax_get_category_name_by_id'
            )),
            'div' => array(
                'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
            ),
        )
    );
    ?>
    <?php
    if (isset($supplier_files) && $supplier_files) {
        echo $this->element('../Suppliers/Elements/form_attached_files');
    }
    ?>
    <div>
        <?php
        if ($action == ConstantsActionsNames::EDIT) {
            echo $this->Html->link(
                __t('Suppliers.Delete'),
                array(
                    'controller' => 'suppliers',
                    'action' => 'delete',
                    $supplier['Supplier']['id'],
                ),
                array(
                    'escape' => false,
                    'class' => 'delete-supplier-js aag-button medium red m-top-1',
                    'data-confirmmsg' => __t('Suppliers.Confirm_delete'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                )
            )
        ?>
        <?php } ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>

<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click:false">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>