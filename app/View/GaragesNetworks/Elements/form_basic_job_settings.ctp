<?php
echo $this->Form->create(
    'GarageNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
    $newPrice = 'New price';
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php
            if(isset($edit_genarts_families))
            {
                echo $this->element('Comun/form_actions', $cancel_action);
            }
            else
            {
                echo $this->Html->link(__t('General.Back'), CakeSession::read('url_referer'), array('class' => 'aag-button medium four'));
                echo $this->Html->link(
                    __t('General.Edit'),
                    array(
                        'controller' => 'garages_networks',
                        'action' => 'edit_genarts_families',
                        $garage_network_id
                    ),
                    array(
                        'escape' => false,
                        'title' => __t('General.Edit'),
                        'class' => 'aag-button medium'
                    )
                );
            }
            ?>
        </div>
    </div>
    <div class="cnt-legend">
        <div>
            <?php
            echo "*" . __t('General.Prices') . " ";
            $txtWithVat = $with_vat ? 'General.With_vat' : 'General.Without_vat';
            echo __t($txtWithVat);
            ?>
        </div>
        <div>
            <span class="icon-legend c-exito"></span> <?php echo __t('GarageNetwork.Applied_price'); ?>
        </div>
    </div>
    <div class="o-auto p-bottom-1">
        <table class="table-tracking tl-fixed">
            <tbody>
                <tr>
                    <th colspan="9" class="ta-left">
                        <div class="aag-title">
                            <?php echo __t('GarageNetwork.Generic_job'); ?>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="ta-left"><?php echo __t('GarageNetwork.Genarts_families'); ?></th>
                    <th colspan="2" class="ta-left"><?php echo __t('General.Type'); ?></th>
                    <th colspan="2" class="ta-left"><?php echo __t('Garage.Garage'); ?></th>
                    <th colspan="2"></th>
                </tr>

                <?php if(!empty($work_dealer)) { ?>
                <tr>
                    <td colspan="4" class="ta-left"><?php echo __t('Genart.All_products_from_HaynesPro'); ?></td>
                    <td colspan="2" class="ta-left">
                        <?php
						$type = "";
                        $tableColumnTitle = "";
                        $value = $garage_network['GarageNetwork']['dealer_'.$work_dealer_type] ?? "";
                        if($work_dealer_type == 'discount')
                        {
							$type = 'discount';
                            $tableColumnTitle = 'Network.Discount';
                        }
                        else
                        {
							$type = $work_dealer_type == 'surcharge' ? 'surcharge' : 'markup';
							if(isset($edit_genarts_families)) {
								echo $this->Form->input(
									'type_family',
									array(
										'label' => false,
										'type' => 'select',
										'options' => array(
											'markup' => __t('Network.Markup'),
											'surcharge' => __t('Network.Surcharge')
										),
										'empty' => false,
										'value' => $type,
										'class' => 'type_family_garage-js',
										'data-element_id' => $garage_network['GarageNetwork']['id'],
									)
								);
							}
							else
							{
								if ($work_dealer_type == 'markup') {
									$tableColumnTitle = 'Network.Markup';
								}
								if ($work_dealer_type == 'surcharge') {
									$tableColumnTitle = 'Network.Surcharge';
								}
							}
                        }
                        echo __t($tableColumnTitle);
                        ?>
                    </td>
                    <td colspan="2" class="ta-left">
                        <?php
                            $placeholder = 'New ' . $type . ($type == 'surcharge' ? '' : ' (%)');
                            if (isset($edit_genarts_families)){
                                echo $this->Form->input(
                                    'genart_dealer.'.$work_dealer_type,
                                    array(
                                        'type' => 'number',
                                        'label' => false,
                                        'class' => 'w-15 f-right genart_input_' . $garage_network['GarageNetwork']['id'] .'-js',
                                        'placeholder' => $placeholder,
                                        'value' => $value,
                                        'style' => 'height:20%',
                                    )
                                );
								if ($work_dealer_type !== 'discount') {
									$otherType = ($type == 'markup') ? 'surcharge' : 'markup';
									$otherTypeValue = $garage_network['GarageNetwork']['dealer_'.$otherType] ?? "";
									$otherPlaceholder = 'New ' . $otherType . ($otherType == 'surcharge' ? '' : ' (%)');
									echo $this->Form->input(
										'genart_dealer.' . $otherType,
										array(
											'type' => 'number',
											'min' => 0,
											'label' => false,
											'data-genart-id' => $garage_network['GarageNetwork']['id'],
											'class' => 'w-15 f-right genart-js genart_input_' . $garage_network['GarageNetwork']['id'] .'-js',
											'placeholder' => $otherPlaceholder,
											'value' => $otherTypeValue,
											'disabled' => true,
											'style' => 'height: 20%; display: none;'
									));
								}
                            } else {
                                if (!empty($value)){
                                    echo $value . ($work_dealer_type == 'surcharge' ? '' : '%');
                                } else {
                                    echo '-';
                                }
                            }
                        ?>
                    </td>
                    <td colspan="2" class="ta-left"></td>
                </tr>
                <?php } ?>
                <?php
                    foreach($genarts_families as $genart_family)
                    {
                        $type = "";
                        $tableColumnTitle = "";
                        if($discount)
                        {
                            $type = "discount";
                            $tableColumnTitle = 'Network.Discount';
                        }
                        else
                        {
                            $type = isset($genart_family['GenartFamily']['surcharge']) && !empty($genart_family['GenartFamily']['surcharge']) ? 'surcharge' : 'markup';
							if (isset($genart_family['GenartFamily']['markup']) && !empty($genart_family['GenartFamily']['markup'])) {
								$tableColumnTitle = 'Network.Markup';
							}
							if (isset($genart_family['GenartFamily']['surcharge']) && !empty($genart_family['GenartFamily']['surcharge'])) {
								$tableColumnTitle = 'Network.Surcharge';
							}
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="ta-left">
                                <div class="flex ai-center gap-1">
                                    <span data-tooltip aria-haspopup="true" class="aag-icon-listado has-tip" title="
                                    <?php
                                    echo 'Genarts: ';
                                    if (isset($genart_family['GenartFamily']['genart_markup'])) {
                                            foreach ($genart_family['GenartFamily']['genart_markup'] as $genart){
                                                echo $genart['GenartMaster']['name_'.$language_code].' ';
                                                }
                                            }
                                            if (isset($genart_family['GenartFamily']['genart_surcharge'])){
                                                foreach ($genart_family['GenartFamily']['genart_surcharge'] as $genart){
                                                    echo $genart['GenartMaster']['name_'.$language_code].' ';
                                                }
                                            }
                                            if (isset($genart_family['GenartFamily']['genart_discount'])){
                                                foreach ($genart_family['GenartFamily']['genart_discount'] as $genart){
                                                    echo $genart['GenartMaster']['name_'.$language_code].' ';
                                                }
                                            }
                                        ?>">
                                    </span>
                                    <?php echo $genart_family['GenartFamily']['name_'.$language_code]; ?>
                                </div>
                            </td>
                            <td colspan="2" class="ta-left">
                                <?php
								    $genart_family_id = $genart_family['GenartFamily']['id'];
									$typeValue = isset($genart_family['GenartFamily'][$type]) ? $genart_family['GenartFamily'][$type] : '-';
									if(isset($edit_genarts_families) && !$discount) {
										echo $this->Form->input(
											'type_family',
											array(
												'label' => false,
												'type' => 'select',
												'options' => array(
													'markup' => __t('Network.Markup'),
													'surcharge' => __t('Network.Surcharge')
												),
												'empty' => false,
												'value' => $type,
												'class' => 'type_family_garage-js',
												'data-element_id' => $genart_family_id,
											)
										);
									}
									else
									{
										echo __t($tableColumnTitle);
									}
								?>
                            </td>
                            <td colspan="2" class="ta-left">
                                <?php
                                $placeholder = 'New '. $type . ($type == 'surcharge' ? '' : ' (%)');
                                if (isset($edit_genarts_families)){
                                    echo $this->Form->input(
                                        'genart_family.'.$type .'.'. $genart_family_id,
                                        array(
                                            'type' => 'number',
                                            'label' => false,
                                            'class' => 'w-15 f-right genart_input_' . $genart_family_id .'-js',
                                            'placeholder' => $placeholder,
                                            'value' => $typeValue,
                                            'style' => 'height:20%',
                                        )
                                    );
									if (!$discount) {
										$otherType = ($type == 'markup') ? 'surcharge' : 'markup';
										$otherTypeValue = $genart_family['GenartFamily'][$otherType] ?? "";
										$otherPlaceholder = 'New ' . $otherType . ($otherType == 'surcharge' ? '' : ' (%)');
										echo $this->Form->input(
											'genart_family.' . $otherType .'.'. $genart_family_id,
											array(
												'type' => 'number',
												'min' => 0,
												'label' => false,
												'class' => 'w-15 f-right genart-js genart_input_' . $genart_family_id .'-js',
												'placeholder' => $otherPlaceholder,
												'value' => $otherTypeValue,
												'disabled' => true,
												'style' => 'height: 20%; display: none;'
										));
									}
                                }
                                else
                                {
                                    if (!empty($genart_family['GenartFamily'][$type])) {
                                        echo $type == 'surcharge' ? $genart_family['GenartFamily'][$type] : $genart_family['GenartFamily'][$type] . '%';
                                    } else {
                                        echo '-';
                                    }
                                }
                                ?>
                            </td>
                            <td colspan="2" class="ta-left"></td>
                        </tr>
                   <?php
                    }
                ?>
                <?php if(isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup']) || isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge'])) { ?>
					<tr>
						<td colspan="4" class="ta-left">
							<div class="flex ai-center gap-1">
								<span data-tooltip aria-haspopup="true" class="aag-icon-listado" title="
								<?php
								$columnName = isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge']) ? 'genarts_surcharge' : 'genarts_markup';
								if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily'][$columnName])) {
									echo 'Genarts: ';
									foreach ($garage_network_genart_no_family['GarageNetworkGenartFamily'][$columnName] as $genart){
										echo $genart['GenartMaster']['name_'.$language_code].' ';
									}
								}
								?>">
								</span>
								<?php echo __t('Genart.Other_parts') ?>
							</div>
						</td>
						<td colspan="2" class="ta-left">
							<?php
								$type = isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge']) && !empty($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge']) ? 'surcharge' : 'markup';
								if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup']) && !empty($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup'])) {
									$tableColumnTitle = 'Network.Markup';
								}
								if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge']) && !empty($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge'])) {
									$tableColumnTitle = 'Network.Surcharge';
								}
								if(isset($edit_genarts_families)) {
									echo $this->Form->input(
										'type_family',
										array(
											'label' => false,
											'type' => 'select',
											'options' => array(
												'markup' => __t('Network.Markup'),
												'surcharge' => __t('Network.Surcharge')
											),
											'empty' => false,
											'value' => $type,
											'class' => 'type_family_garage-js',
											'data-element_id' => $garage_network_genart_no_family['GarageNetworkGenartFamily']['id'],
										)
									);
								}
								else
								{
									echo __t($tableColumnTitle);
								}
							?>
						</td>
						<td colspan="2" class="ta-left">
							<?php
							$placeholder = 'New '. __t($type) . ($type == 'surcharge' ? '' : ' (%)');
							$value = $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] ?? '-';
							if (isset($edit_genarts_families)){
								echo $this->Form->input(
									'genart_no_family.'.$type,
									array(
										'type' => 'number',
										'label' => false,
										'class' => 'w-15 f-right genart_input_' . $garage_network_genart_no_family['GarageNetworkGenartFamily']['id'] .'-js',
										'placeholder' => $placeholder,
										'value' => $value,
										'style' => 'height:20%',
									)
								);
								$otherType = ($type == 'markup') ? 'surcharge' : 'markup';
								$otherTypeValue = $garage_network_genart_no_family['GarageNetworkGenartFamily'][$otherType] ?? "";
								$otherPlaceholder = 'New ' . $otherType . ($otherType == 'surcharge' ? '' : ' (%)');
								echo $this->Form->input(
									'genart_no_family.' . $otherType .'.'. $garage_network_genart_no_family['GarageNetworkGenartFamily']['id'],
									array(
										'type' => 'number',
										'min' => 0,
										'label' => false,
										'class' => 'w-15 f-right genart-js genart_input_' . $garage_network_genart_no_family['GarageNetworkGenartFamily']['id'] .'-js',
										'placeholder' => $otherPlaceholder,
										'value' => $otherTypeValue,
										'disabled' => true,
										'style' => 'height: 20%; display: none;'
								));
							} else {
								if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily'][$type]) && $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] != ''){
									echo $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] . ($type == 'surcharge' ? '' : ' %');
								} else {
									echo '-';
								}
							}
							?>
						</td>
						<td colspan="2" class="ta-left"></td>
					</tr>
                <?php
                } else {
                    if(isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['genarts_discount'])) { ?>
                    <tr>
                        <td colspan="4" class="ta-left">
                            <div class="flex ai-center gap-1">
                                <span data-tooltip aria-haspopup="true" class="aag-icon-listado has-tip" title="
                                <?php
                                echo 'Genarts: ';
                                if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['genarts_discount'])) {
                                    foreach ($garage_network_genart_no_family['GarageNetworkGenartFamily']['genarts_discount'] as $genart){
                                        echo $genart['GenartMaster']['name_'.$language_code].' ';
                                        }
                                }?>">
                                </span>
                                <?php echo __t('Genart.Other_parts') ?>
                            </div>
                        </td>
                        <td colspan="2" class="ta-left">
                            <?php
                            $tableColumnTitle = "";
                            if (!empty($garage_network_genart_no_family)){
                                if($discount)
                                {
                                    $tableColumnTitle = 'Network.Discount';
                                    $type = 'discount';
                                }
                                else
                                {
                                    // shows 'markup' if it's not null or if 'markup' and 'surcharge' are both null
                                    $isMarkup = $garage_network_genart_no_family['GarageNetworkGenartFamily']['markup'] != null ||
                                                ($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup'] == null &&
                                                $garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge'] == null);
                                    $type = $isMarkup ? 'markup' : 'surcharge';
									if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup']) && !empty($garage_network_genart_no_family['GarageNetworkGenartFamily']['markup'])) {
										$tableColumnTitle = 'Network.Markup';
									}
									if (isset($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge']) && !empty($garage_network_genart_no_family['GarageNetworkGenartFamily']['surcharge'])) {
										$tableColumnTitle = 'Network.Surcharge';
									}
                                }
                                echo __t($tableColumnTitle);
                            } else {
                                if($discount){
                                    $tableColumnTitle = 'Network.Discount';
                                    $type = 'discount';
                                }else{
                                    if(!$is_surcharge){
                                        $tableColumnTitle = 'Network.Markup';
                                        $type = 'markup';
                                    } else {
                                        $tableColumnTitle = 'Network.Surcharge';
                                        $type = 'surcharge';
                                    }
                                }
                                echo __t($tableColumnTitle);
                            }
                            ?>
                        </td>
                        <td colspan="2" class="ta-left">
                            <?php
                            $placeholder = 'New '. $type . ($type == 'surcharge' ? '' : ' (%)');
                            $value = $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] ?? '-';
                            if (isset($edit_genarts_families)){
                                echo $this->Form->input(
                                    'genart_no_family.'.$type,
                                    array(
                                        'type' => 'number',
                                        'label' => false,
                                        'class' => 'w-15 f-right',
                                        'placeholder' => $placeholder,
                                        'value' => $value,
                                        'style' => 'height:20%',
                                    )
                                );
                            } else {
                                if (!empty($garage_network_genart_no_family['GarageNetworkGenartFamily'][$type])){
                                    echo $type == 'surcharge' ? $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] : $garage_network_genart_no_family['GarageNetworkGenartFamily'][$type] .'%';
                                } else {
                                    echo '-';
                                }
                            }
                            ?>
                        </td>
                        <td colspan="2" class="ta-left"></td>
                    </tr>
                <?php }
            } ?>
                <tr>
                    <th colspan="4" class="ta-left"><?php echo 'Labour'; ?></th>
                    <th colspan="2" class="ta-left"></th>
                    <th colspan="2" class="ta-left"><?php echo __t('Garage.Garage');?></th>
                    <th colspan="2" class="ta-left"><?php echo __t('General.Network_price'); ?></th>
                </tr>
                <tr>
                    <td colspan="4" class="ta-left"><?php echo  __t('Network.Labour_price'); ?></td>
                    <td colspan="2" class="ta-left"></td>
                    <td colspan="2" class="ta-left">
                        <?php
                            if(isset($edit_genarts_families))
                            {
								if (!empty($network['Network']['min_labour_price']) && !empty($network['Network']['max_labour_price'])) {
									echo '<div class="cnt-range-with-values">';
									echo '<div>' .$network['Network']['min_labour_price'] . '</div>';
									echo '<div>' .$network['Network']['max_labour_price'] . '</div>';
									echo $this->Form->input(
										'labour_price_slider',
										array(
											'type' => 'range',
											'label' => false,
											'min' => $network['Network']['min_labour_price'],
											'max' => $network['Network']['max_labour_price'],
											'value' => $garage_network['GarageNetwork']['labour_hourly_price'],
											'step' => isset($network['Network']['slider_increment']) ? $network['Network']['slider_increment'] : '0.01',
											'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
									));
									echo $this->Form->input(
										'labour_price',
										array(
											'type' => 'number',
											'style' => 'display: none;',
											'label' => false,
											'value' => $garage_network['GarageNetwork']['labour_hourly_price'],
									));
									?>
									<output>
										<?php echo $garage_network['GarageNetwork']['labour_hourly_price']; ?>
									</output></div><?php
								} else {
									echo $this->Form->input(
										'labour_price',
										array(
											'type' => 'number',
											'label' => false,
											'class' => 'w-15 f-right',
											'placeholder' => $newPrice,
											'value' => $garage_network['GarageNetwork']['labour_hourly_price'],
											'style' => 'height:20%',
									));
								}
                            }
                            else
                            {
                                if (!empty($garage_network['GarageNetwork']['labour_hourly_price'])){
                                    ?>
                                <div style="background-color: #BDECB6">
                                    <strong>
                                <?php
                                }
                                if(!empty($garage_network['GarageNetwork']['labour_hourly_price']))
                                {
                                    echo $garage_network['GarageNetwork']['labour_hourly_price'];
                                }
                                else
                                {
                                    echo "-";
                                }
                            }
                            ?>
                                </strong>
                            </div>
                    </td>
                    <td colspan="2" class="ta-left">
                        <?php
                        if (empty($garage_network['GarageNetwork']['labour_hourly_price']) && !empty($network['Network']['labour_hourly_price'])){
                        ?>
                        <div style="background-color: #BDECB6">
                            <strong>
                                <?php
                                }
                                if(!empty($network['Network']['labour_hourly_price'])){
                                    echo $network['Network']['labour_hourly_price'];
                                }else{
                                    echo '-';
                                }
                                ?>
                            </strong>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="ta-left"><?php echo  __t('Network.Labour_price_electric'); ?></td>
                    <td colspan="2" class="ta-left"></td>
                    <td colspan="2" class="ta-left">
                                <?php
                                if(isset($edit_genarts_families))
                                {
									if (!empty($network['Network']['min_labour_price_ev']) && !empty($network['Network']['min_labour_price_ev'])) {
										echo '<div class="cnt-range-with-values">';
										echo '<div>' . $network['Network']['min_labour_price_ev'] . '</div>';
										echo '<div>' . $network['Network']['max_labour_price_ev'] . '</div>';
										echo $this->Form->input(
											'labour_price_electric_slider',
											array(
												'type' => 'range',
												'label' => false,
												'min' => $network['Network']['min_labour_price_ev'],
												'max' => $network['Network']['max_labour_price_ev'],
												'value' => $garage_network['GarageNetwork'] ['labour_hourly_price_electric_vehicles'],
												'step' => isset($network['Network']['slider_increment_ev']) ? $network['Network']['slider_increment_ev'] : '0.01',
												'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
										));
										echo $this->Form->input(
											'labour_price_electric',
											array(
												'type' => 'number',
												'label' => false,
												'class' => 'w-15 f-right',
												'placeholder' => $newPrice,
												'value' => $garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'],
												'style' => 'display:none',
										));

										?><output><?php echo $garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles']; ?></output></div><?php
									} else {
										echo $this->Form->input(
											'labour_price_electric',
											array(
												'type' => 'number',
												'label' => false,
												'class' => 'w-15 f-right',
												'placeholder' => $newPrice,
												'value' => $garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'],
												'style' => 'height:20%',
										));
									}
                                }
                                else
                                {
                                    if(!empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'])){
                                    ?>
                                    <div style="background-color: #BDECB6">
                                        <strong>
                                            <?php
                                            }
                                            if(!empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles']))
                                            {
                                                echo $garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'];
                                            }
                                            else
                                            {
                                                echo "-";
                                            }
                                }
                                ?>
                                        </strong>
                                    </div>
                    </td>
                    <td colspan="2" class="ta-left">
                        <?php if (!empty($network['Network']['labour_hourly_price_electric_vehicles']) && empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'])){
                        ?>
                        <div style="background-color: #BDECB6">
                            <strong>
                                <?php
                                }
                                if(!empty($network['Network']['labour_hourly_price_electric_vehicles'])){
                                    echo $network['Network']['labour_hourly_price_electric_vehicles'];
                                }else{
                                    echo '-';
                                }
                                ?>
                            </strong>
                        </div>
                    </td>
                </tr>
                    <?php foreach ($genarts_with_is_labour_time as $genart){ ?>
                        <tr>
                            <td colspan="4" class="ta-left">
                                <?php echo $genart['GenartMaster']['name_'.$language_code].'' ?>
                                <?php if ($genart['GenartMaster']['include_vat']) { ?>
                                    <sub class="fw-bold">(<?php echo __t('General.Tax_free'); ?>)</sub>
                                <?php } ?>
                            </td>
                            <td colspan="2" class="ta-left"></td>
                            <td colspan="2" class="ta-left">
                                <?php
                                if(isset($edit_genarts_families) )
                                {
									if (!empty($genart['GenartMaster']['min_labour_price']) && !empty($genart['GenartMaster']['max_labour_price'])) {
										echo '<div class="cnt-range-with-values">';
										echo '<div>' . $genart['GenartMaster']['min_labour_price'] . '</div>';
										echo '<div>' .$genart['GenartMaster']['max_labour_price'] . '</div>';
										echo $this->Form->input(
											'labour_price_electric_slider',
											array(
												'type' => 'range',
												'label' => false,
												'min' => $genart['GenartMaster']['min_labour_price'],
												'max' => $genart['GenartMaster']['max_labour_price'],
												'value' => $genart['GarageNetworkGenartMaster']['genart_master_labour_price'],
												'step' => isset($genart['GenartMaster']['slider_increment']) ? $genart['GenartMaster']['slider_increment'] : '0.01',
												'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
											));
										echo $this->Form->input(
											'GenartMaster.'.$genart['GenartMaster']['id'].'.genart_master_labour_price',
											array(
												'type' => 'number',
												'label' => false,
												'style' => 'display: none;',
												'value' => $genart['GarageNetworkGenartMaster']['genart_master_labour_price'],
										));

										?><output><?php echo $genart['GarageNetworkGenartMaster']['genart_master_labour_price']; ?></output></div><?php
									} else {
										echo $this->Form->input(
											'GenartMaster.'.$genart['GenartMaster']['id'].'.genart_master_labour_price',
											array(
												'type' => 'number',
												'label' => false,
												'class' => 'w-15 f-right',
												'placeholder' => $newPrice,
												'value' => $genart['GarageNetworkGenartMaster']['genart_master_labour_price'],
												'style' => 'height:20%',
										));
									}
                                }
                                else
                                {
                                    if(!empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price'])){
                                    ?>
                                    <div style="background-color: #BDECB6">
                                        <strong>
                                            <?php
                                            }
                                            if(!empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price']))
                                            {
                                                echo $genart['GarageNetworkGenartMaster']['genart_master_labour_price'];
                                            }
                                            else
                                            {
                                                echo "-";
                                            }
                                }
                                ?>
                                        </strong>
                                    </div>
                            </td>
                            <td colspan="2" class="ta-left">
                                <?php if (!empty($genart['GenartMaster']['price_labour_time']) && empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price'])){
                                ?>
                                <div style="background-color: #BDECB6">
                                    <strong>
                                        <?php
                                        }
                                        if(!empty($genart['GenartMaster']['price_labour_time'])){
                                            echo $genart['GenartMaster']['price_labour_time'];
                                        }else{
                                            echo '-';
                                        }
                                        ?>
                                    </strong>
                                </div>
                            </td>
                    </tr>
                    <?php } ?>
            </tbody>
        </table>
    </div>
<?php echo $this->Form->end(); ?>
