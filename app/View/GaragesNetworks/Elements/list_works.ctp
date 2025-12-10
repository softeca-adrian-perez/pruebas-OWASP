<tr>
    <th colspan="7" style="background-color: #fff !important;">
        <div class="aag-title">
            <?php echo $work['Work']['name_' . $language_code]; ?>
        </div>
    </th>
</tr>
<?php
if (!isset($work['Work']['onlyLabourTimeGenarts']) || (isset($work['Work']['onlyLabourTimeGenarts']) && !$work['Work']['onlyLabourTimeGenarts'])) {
?>
<tr>
    <th class="ta-left"><?php echo __t('GarageNetwork.Product_groups'); ?></th>
    <th class="one ta-left"><?php echo __t('General.Type_garage'); ?></th>
    <th class="one ta-left"><?php echo __t('Garage.Garage'); ?></th>
	<th class="two ta-left"><?php echo __t('General.Type_family'); ?></th>
    <th class="two ta-left"><?php echo __t('GarageNetwork.Family'); ?></th>
	<th class="one ta-left"><?php echo __t('General.Type_default'); ?></th>
    <th class="one ta-left"><?php echo __t('General.Network_price_advanced'); ?></th>
</tr>
<?php
if(!empty($work['Work']['genarts'])) {
    foreach($work['Work']['genarts'] as $genart) {
		$isMarkupTypeGarage = null;
        $type = $typeGarage = $tableColumnTitle = $tableColumnTitleTypeGarage = "";
        if(isset($genart['GenartMaster']['is_labour_time']) && $genart['GenartMaster']['is_labour_time'] != ConstantsBooleans::NO_ACTIVE) {
            continue;
        }
        if($discount) {
            $type = $typeGarage = "discount";
            $tableColumnTitle = $tableColumnTitleTypeGarage = 'Network.Discount';
        }
        else {
            // shows 'markup' if it's not null or if 'markup' and 'surcharge' are both null
            $isMarkup = $genart['Genart']['markup'] != null ||
                        ($genart['Genart']['markup'] == null &&
                        $genart['Genart']['surcharge'] == null);
            $type = $isMarkup ? 'markup' : 'surcharge';
            $tableColumnTitle = $isMarkup ? 'Network.Markup' : 'Network.Surcharge';

			// The value of the garage is displayed, otherwise the value of the network is displayed
			if (isset($genart['GarageNetworkGenart']['markup']) && !empty($genart['GarageNetworkGenart']['markup'])) {
				$isMarkupTypeGarage = true;
			} elseif (isset($genart['GarageNetworkGenart']['surcharge']) && !empty($genart['GarageNetworkGenart']['surcharge'])) {
				$isMarkupTypeGarage = false;
			}
			if (isset($isMarkupTypeGarage)) {
				$typeGarage = $isMarkupTypeGarage ? 'markup' : 'surcharge';
				$tableColumnTitleTypeGarage = $isMarkupTypeGarage ? 'Network.Markup' : 'Network.Surcharge';
			} else {
				if (isset($genart['Genart']['garage_network_genart_family_type']) && !empty($genart['Genart']['garage_network_genart_family_type'])) {
					$isMarkupTypeGarage = $genart['Genart']['garage_network_genart_family_type'] == 'markup' ? true : false;
					$typeGarage = $genart['Genart']['garage_network_genart_family_type'];
					$tableColumnTitleTypeGarage = $genart['Genart']['garage_network_genart_family_type_text'];
				} else {
					$isMarkupTypeGarage = $isMarkup;
					$typeGarage = $type;
					$tableColumnTitleTypeGarage = $tableColumnTitle;
				}
			}
        }
        ?>
        <tr>
            <td class="ta-left">
                <div class="flex ai-center gap-1">
                    <?php
                    if(!isset($genart['Genart']['fluid']) || (isset($genart['Genart']['fluid']) && $genart['Genart']['has_advanced_settings'])) {
                        ?> <span data-tooltip aria-haspopup="true" class="aag-icon-listado has-tip" title="<?php echo isset($genart['Genart']['genart_family']) ? __t('GarageNetwork.Genarts_families').': '.$genart['Genart']['genart_family']['name_'.$language_code] : __t('GarageNetwork.Genarts_families').': '.__t('Genart.Other_parts')?>"></span> <?php
                    }
                    echo $genart['Genart']['name_' . $language_code];
                    ?>
                </div>
            </td>
            <td class="one ta-left">
                <?php
                $typeValue = $genart['Genart']['garagePrices']['GarageNetworkGenart'][$typeGarage] ?? "";
                if(isset($edit_genarts) && !$discount) {
                    echo $this->Form->input(
                        'type_garage',
                        array(
                            'label' => false,
                            'type' => 'select',
                            'options' => array(
                                'markup' => __t('Network.Markup'),
                                'surcharge' => __t('Network.Surcharge')
                            ),
                            'empty' => false,
                            'value' => $typeGarage,
                            'class' => 'type_family_garage-js',
                            'data-element_id' => $genart['Genart']['id'],
                        )
                    );
                }
                else
                {
                    echo __t($tableColumnTitleTypeGarage);
                }
				?>
            </td>
            <td class="one ta-left">
                <?php
                if(isset($edit_genarts)) {
                    $idGenart = $genart['Genart']['id'];
                    $placeholder = 'New ' . $typeGarage . (($discount || $isMarkupTypeGarage ? ' (%)' : ''));
                    echo $this->Form->input(
                        'genart_' . $typeGarage . '.' . $idGenart,
                        array(
                            'type' => 'number',
							'min' => 0,
                            'label' => false,
                            'data-genart-id' => $idGenart,
                            'class' => 'w-15 f-right genart-js genart_input_' . $idGenart .'-js',
                            'placeholder' => $placeholder,
                            'value' => $typeValue,
                            'style' => 'height:20%'
                    ));
					if(!$discount) {
						$otherType = ($typeGarage == 'markup') ? 'surcharge' : 'markup';
						$otherTypeValue = $genart['Genart']['garagePrices']['GarageNetworkGenart'][$otherType] ?? "";
						$otherPlaceholder = 'New ' . $otherType . ($isMarkupTypeGarage ? '' : ' (%)');
						echo $this->Form->input(
							'genart_' . $otherType . '.' . $idGenart,
							array(
								'type' => 'number',
								'min' => 0,
								'label' => false,
								'data-genart-id' => $idGenart,
								'class' => 'w-15 f-right genart-js genart_input_' . $idGenart .'-js',
								'placeholder' => $otherPlaceholder,
								'value' => $otherTypeValue,
								'disabled' => true,
								'style' => 'height: 20%; display: none;'
						));
					}
                }
                else
                {
                    if(!empty($genart['Genart']['garagePrices']['GarageNetworkGenart'][$typeGarage])) {
                        ?>
                        <div style="background-color: #BDECB6">
                            <strong>
                                <?php
                    }
                                if(!empty($genart['Genart']['garagePrices']) && !empty($typeValue)) {
                                    echo $discount || $isMarkupTypeGarage ? $typeValue . "%" : $typeValue;
                                }
                                else { echo "-"; }
                    if(!empty($genart['Genart']['garagePrices']['GarageNetworkGenart'][$typeGarage])) {
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                }
                ?>
            </td>
			<td class="two ta-left">
                <?php
                    echo isset($genart['Genart']['garage_network_genart_family_type_text']) && !empty($genart['Genart']['garage_network_genart_family_type_text']) ?
					__t($genart['Genart']['garage_network_genart_family_type_text']) : __t($tableColumnTitle);
				?>
            </td>
            <td class="two ta-left">
                <?php
                if(empty($genart['Genart']['garagePrices']['GarageNetworkGenart'][$typeGarage]) && !empty($genart['Genart']['garage_network_genart_family'])) {
                    ?>
                    <div style="background-color: #BDECB6">
                        <strong>
                            <?php
                }
                            if(!empty($genart['Genart']['garage_network_genart_family'])) {
                                echo $genart['Genart']['garage_network_genart_family_type'] !== 'surcharge' ? $genart['Genart']['garage_network_genart_family'] . '%' : $genart['Genart']['garage_network_genart_family'];
                            }
                            else { echo '-'; }
                if(empty($genart['Genart']['garagePrices']['GarageNetworkGenart'][$typeGarage]) && !empty($genart['Genart']['garage_network_genart_family'])) {
                            ?>
                        </strong>
                    </div>
                    <?php
                }
                ?>
            </td>
			<td class="one ta-left">
                <?php echo __t($tableColumnTitle); ?>
            </td>
            <td class="one ta-left">
                <?php
                if(!empty($genart['Genart'][$type]) && empty($genart['Genart']['garagePrices']) && empty($genart['Genart']['garage_network_genart_family'])) {
                    ?>
                    <div style="background-color: #BDECB6">
                        <strong>
                           <?php
                }
                            if(!empty($genart['Genart'][$type])) {
                                $value = $genart['Genart'][$type];
                                echo $discount || $isMarkup ? $value . "%" : $value;
                            }
                            else { echo "-"; }
                if(!empty($genart['Genart'][$type]) && empty($genart['Genart']['garagePrices']) && empty($genart['Genart']['garage_network_genart_family']))  {
                            ?>
                        </strong>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
        <?php
    }
}
else
{
    $type = $typeFamily = $typeGarage = "";
    $tableColumnTitle = $tableColumnTitleTypeFamily = $tableColumnTitleTypeGarage = "";
    if($discount) {
        $type = $typeFamily = $typeGarage = "discount";
        $tableColumnTitle = $tableColumnTitleFamily = $tableColumnTitleGarage = 'Network.Discount';
    }
    else {
        // shows 'markup' if it's not null or if 'markup' and 'surcharge' are both null
        $isMarkup = $work['Work']['markup'] != null ||
                    ($work['Work']['markup'] == null &&
                    $work['Work']['surcharge'] == null);
        $type = $isMarkup ? 'markup' : 'surcharge';
        $tableColumnTitle = $isMarkup ? 'Network.Markup' : 'Network.Surcharge';

        // The value of the family is displayed, otherwise the value of the network is displayed
        if (
            isset($garage_network['GarageNetwork']) &&
            ($garage_network['GarageNetwork']['dealer_markup'] != null || $garage_network['GarageNetwork']['dealer_surcharge'] != null)
        ) {
            $isMarkupTypeFamily = $garage_network['GarageNetwork']['dealer_markup'] != null;
        } else {
            $isMarkupTypeFamily = $isMarkup;
        }
        $typeFamily = $isMarkupTypeFamily ? 'markup' : 'surcharge';
        $tableColumnTitleTypeFamily = $isMarkupTypeFamily ? 'Network.Markup' : 'Network.Surcharge';

        // The value of the garage is displayed, otherwise the value of the family is displayed
        if (
            isset($work['Work']['GarageNetworkWorkPrice']) &&
            ($work['Work']['GarageNetworkWorkPrice']['markup'] != null || $work['Work']['GarageNetworkWorkPrice']['surcharge'] != null)
        ) {
            $isMarkupTypeGarage = $work['Work']['GarageNetworkWorkPrice']['markup'] != null;
        } else {
            $isMarkupTypeGarage = $isMarkupTypeFamily;
        }
        $typeGarage = $isMarkupTypeGarage ? 'markup' : 'surcharge';
        $tableColumnTitleTypeGarage = $isMarkupTypeGarage ? 'Network.Markup' : 'Network.Surcharge';
    }
    ?>
    <tr>
        <td class="ta-left">
            <?php echo __t("Genart.All_products_from_HaynesPro"); ?>
        </td>
        <td class="one ta-left">
            <?php
				$typeValue = $work['Work']['GarageNetworkWorkPrice'][$typeGarage] ?? "";
				if(isset($edit_genarts) && !$discount) {
					echo $this->Form->input(
						'type_garage',
						array(
							'label' => false,
							'type' => 'select',
							'options' => array(
								'markup' => __t('Network.Markup'),
								'surcharge' => __t('Network.Surcharge')
							),
							'empty' => false,
							'value' => $typeGarage,
							'class' => 'type_family_garage-js',
							'data-element_id' => $work['Work']['id'],
						)
					);
				}
				else
				{
					echo __t($tableColumnTitleTypeGarage);
				}
			?>
        </td>
        <td class="one ta-left">
            <?php
            if(isset($edit_genarts))
            {
                $placeholder = 'New ' . $typeGarage;
                $placeholder .= $discount || $isMarkup ? ' (%)' : '';
                echo $this->Form->input(
                    'works_prices.' . $work['Work']['id'] . '.' . $typeGarage,
                    array(
                        'type' => 'number',
                        'label' => false,
                        'class' => 'w-15 f-right genart_input_' . $work['Work']['id'] .'-js',
                        'placeholder' => $placeholder,
                        'value' => $typeValue,
                        'style' => 'height:20%'
                ));
				if(!$discount) {
					$otherType = ($typeGarage == 'markup') ? 'surcharge' : 'markup';
					$otherTypeValue = $genart['Genart']['garagePrices']['GarageNetworkGenart'][$otherType] ?? "";
					$otherPlaceholder = 'New ' . $otherType . ($isMarkupTypeGarage ? '' : ' (%)');
					echo $this->Form->input(
						'works_prices.' . $work['Work']['id'] . '.' . $otherType,
						array(
							'type' => 'number',
							'min' => 0,
							'label' => false,
							'class' => 'w-15 f-right genart-js genart_input_' . $work['Work']['id'] .'-js',
							'placeholder' => $otherPlaceholder,
							'value' => $otherTypeValue,
							'disabled' => true,
							'style' => 'height: 20%; display: none;'
					));
				}
            }
            else
            {
                if (!empty($typeValue)){ ?>
                <div style="background-color: #BDECB6">
                    <strong>
                <?php
                }
                        if(!empty($typeValue))
                        {
                                echo $discount || $isMarkupTypeGarage ? $typeValue . "%" : $typeValue;
                        }
                        else
                        {
                            echo "-";
                        }
                if (!empty($typeValue)){ ?>
                    </strong>
                </div>
            <?php
                }
            }
            ?>
        </td>
		<td class="two ta-left">
            <?php echo __t($tableColumnTitleTypeFamily); ?>
        </td>
        <td class="two ta-left">
            <?php
            if(empty($work['Work']['GarageNetworkWorkPrice'][$typeGarage]) && !empty($garage_network['GarageNetwork']['dealer_'.$typeFamily])) {
                ?>
                <div style="background-color: #BDECB6">
                    <strong>
                        <?php
            }
			if(!empty($garage_network['GarageNetwork']['dealer_'.$typeFamily])) {
				echo $garage_network['GarageNetwork']['dealer_'.$typeFamily] . ($typeFamily == 'surcharge' ? '' : '%');
			}
			else { echo '-'; }
            if(empty($work['Work']['GarageNetworkWorkPrice'][$typeGarage]) && !empty($garage_network['GarageNetwork']['dealer_'.$typeFamily])) {
                        ?>
                    </strong>
                </div>
                <?php
            }
            ?>
        </td>
		<td class="one ta-left">
            <?php echo __t($tableColumnTitle); ?>
        </td>
        <td class="one ta-left">
            <?php
            if(empty($work['Work']['GarageNetworkWorkPrice'][$typeGarage]) && empty($garage_network['GarageNetwork']['dealer_'.$typeFamily]) && !empty($work['Work'][$type]))  {
                ?>
                <div style="background-color: #BDECB6">
                    <strong>
                        <?php
            }
                        if(!empty($work['Work'][$type])) {
                            $value = $work['Work'][$type];
                            echo $discount || $isMarkup ? $value . "%" : $value;
                        }
                        else { echo "-"; }
            if(!empty($work['Work']['GarageNetworkWorkPrice'][$type]) && empty($work['Work'][$type])) {
                        ?>
                    </strong>
                </div>
                <?php
            }
            ?>
        </td>
    </tr>
    <?php
}
?>
<?php } ?>
<tr>
    <th class="ta-left">
        <?php
        $withVat = $network['Network']['with_vat'] ? 'with_vat' : 'without_vat';
        echo  __t('Network.Labours_' . $withVat);
        ?>
    </th>
    <th class="one"></th>
    <th class="one ta-left"><?php echo __t('Garage.Garage'); ?></th>
	<th class="two"></th>
    <th class="two ta-left"><?php echo __t('GarageNetwork.Family'); ?></th>
	<th class="one"></th>
    <th class="one ta-left"><?php echo __t('General.Network_price_advanced'); ?></th>
</tr>
<?php
if (!isset($work['Work']['onlyLabourTimeGenarts']) || (isset($work['Work']['onlyLabourTimeGenarts']) && !$work['Work']['onlyLabourTimeGenarts'])) {
?>
	<tr>
		<td class="ta-left"><?php echo  __t('Network.Labour_price'); ?></td>
		<td class="one ta-left"></td>
		<td class="one ta-left">
			<?php
			$newPrice = 'New price';
			if(isset($edit_genarts)) {
				if (!empty($network['Network']['min_labour_price']) && !empty($network['Network']['max_labour_price'])) {
					?>
					<div class="cnt-range-with-values">
						<div><?php echo $network['Network']['min_labour_price']; ?></div>
						<div><?php echo $network['Network']['max_labour_price']; ?></div>
						<?php
						echo $this->Form->input(
							'labour_price_works',
							array(
								'type' => 'range',
								'label' => false,
								'min' => $network['Network']['min_labour_price'],
								'max' => $network['Network']['max_labour_price'],
								'value' => $work['Work']['labour_hourly_price'] ?? '',
								'step' => isset($network['Network']['slider_increment']) ? $network['Network']['slider_increment'] : '0.01',
								'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
						));
						echo $this->Form->input(
							'labour_price_works.' . $work['Work']['id'] . '.general',
							array(
								'type' => 'number',
								'label' => false,
								'class' => 'w-15 f-right',
								'placeholder' => $newPrice ?? '',
								'value' => $work['Work']['labour_hourly_price'] ?? '',
								'style' => 'display:none'
						));
						?>
						<output>
							<?php echo $work['Work']['labour_hourly_price'] ?? ''; ?>
						</output>
					</div>
				<?php
				} else {
					echo $this->Form->input(
						'labour_price_works.' . $work['Work']['id'] . '.general',
						array(
							'type' => 'number',
							'label' => false,
							'class' => 'w-15 f-right',
							'placeholder' => $newPrice ?? '',
							'value' => $work['Work']['labour_hourly_price'] ?? '',
							'style' => 'height:20%'
					));
				}
			}
			else
			{
				if(!empty($work['Work']['labour_hourly_price'])) {
					?>
					<div style="background-color: #BDECB6">
						<strong>
							<?php
				}
							if(!empty($work['Work']['labour_hourly_price'])) { echo $work['Work']['labour_hourly_price']; }
							else { echo "-"; }
				if(!empty($work['Work']['labour_hourly_price'])) {
							?>
						</strong>
					</div>
					<?php
				}
			}
			?>
		</td>
		<td class="two ta-left"></td>
		<td class="two ta-left">
			<?php
			if(!empty($garage_network['GarageNetwork']['labour_hourly_price']) && empty($work['Work']['labour_hourly_price'])) {
				?>
				<div style="background-color: #BDECB6">
					<strong>
						<?php
			}
						if(!empty($garage_network['GarageNetwork']['labour_hourly_price'])) { echo $garage_network['GarageNetwork']['labour_hourly_price']; }
						else { echo '-'; }
			if(!empty($garage_network['GarageNetwork']['labour_hourly_price']) && empty($work['Work']['labour_hourly_price'])) {
						?>
					</strong>
				</div>
				<?php
			}
			?>
		</td>
		<td class="one ta-left"></td>
		<td class="one ta-left">
			<?php
			if(!empty($network['Network']['labour_hourly_price']) && empty($work['Work']['labour_hourly_price']) && empty($garage_network['GarageNetwork']['labour_hourly_price'])) {
				?>
				<div style="background-color: #BDECB6">
					<strong>
						<?php
			}
						if(!empty($network['Network']['labour_hourly_price'])) { echo $network['Network']['labour_hourly_price']; }
						else { echo "-"; }
			if(!empty($network['Network']['labour_hourly_price']) && empty($work['Work']['labour_hourly_price']) && empty($garage_network['GarageNetwork']['labour_hourly_price'])) {
						?>
					</strong>
				</div>
				<?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td class="ta-left"><?php echo  __t('Network.Labour_price_electric'); ?></td>
		<td class="one"></td>
		<td class="one ta-left">
			<?php
			if(isset($edit_genarts))
			{
				if (!empty($network['Network']['min_labour_price_ev']) && !empty($network['Network']['max_labour_price_ev'])) {
					?>
					<div class="cnt-range-with-values">
						<div><?php echo $network['Network']['min_labour_price_ev']; ?></div>
						<div><?php echo $network['Network']['max_labour_price_ev']; ?></div>
						<?php
						echo $this->Form->input(
							'labour_price',
							array(
								'type' => 'range',
								'label' => false,
								'min' => $network['Network']['min_labour_price_ev'],
								'max' => $network['Network']['max_labour_price_ev'],
								'value' => $work['Work']['labour_hourly_price_electric_vehicles'] ?? '',
								'step' => isset($network['Network']['slider_increment_ev']) ? $network['Network']['slider_increment_ev'] : '0.01',
								'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
						));
						echo $this->Form->input(
							'labour_price_works.' . $work['Work']['id'] . '.electric',
							array(
								'type' => 'number',
								'label' => false,
								'value' => $work['Work']['labour_hourly_price_electric_vehicles'] ?? '',
								'style' => 'display:none;height:20%'
						));
						?>
						<output>
							<?php echo $work['Work']['labour_hourly_price'] ?? ''; ?>
						</output>
					</div>
				<?php
				} else {
					echo $this->Form->input(
						'labour_price_works.' . $work['Work']['id'] . '.electric',
						array(
							'type' => 'number',
							'label' => false,
							'class' => 'w-15 f-right',
							'placeholder' => $newPrice ?? '',
							'value' => $work['Work']['labour_hourly_price_electric_vehicles'] ?? '',
							'style' => 'height:20%'
					));
				}
			}
			else
			{
				if(!empty($work['Work']['labour_hourly_price_electric_vehicles'])) {
					?>
					<div style="background-color: #BDECB6">
						<strong>
						<?php
				}
						if(!empty($work['Work']['labour_hourly_price_electric_vehicles'])) { echo $work['Work']['labour_hourly_price_electric_vehicles']; }
						else { echo "-"; }
				if(!empty($work['Work']['labour_hourly_price_electric_vehicles'])) {
						?>
						</strong>
					</div>
					<?php
				}
			}
			?>
		</td>
		<td class="two"></td>
		<td class="two ta-left">
			<?php
			if(!empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles']) && empty($work['Work']['labour_hourly_price_electric_vehicles'])) {
				?>
				<div style="background-color: #BDECB6">
					<strong>
						<?php
			}
						if(!empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'])) { echo $garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles']; }
						else { echo '-'; }
			if(!empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles']) && empty($work['Work']['labour_hourly_price_electric_vehicles'])) {
						?>
					</strong>
				</div>
				<?php
			}
			?>
		</td>
		<td class="one"></td>
		<td class="one ta-left">
			<?php
			if(!empty($network['Network']['labour_hourly_price_electric_vehicles']) && empty($work['Work']['labour_hourly_price_electric_vehicles']) && empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'])) {
				?>
				<div style="background-color: #BDECB6">
					<strong>
						<?php
			}
					if(!empty($network['Network']['labour_hourly_price_electric_vehicles'])) { echo $network['Network']['labour_hourly_price_electric_vehicles']; }
					else { echo "-"; }
			if(!empty($network['Network']['labour_hourly_price_electric_vehicles']) && empty($work['Work']['labour_hourly_price_electric_vehicles']) && empty($garage_network['GarageNetwork']['labour_hourly_price_electric_vehicles'])) {
						?>
					</strong>
				</div>
				<?php
			}
			?>
		</td>
	</tr>
<?php } ?>
<?php
if (isset($work['Work']['genarts'])) {
    foreach($work['Work']['genarts'] as $genart) {
        if(isset($genart['GenartMaster']['is_labour_time']) && $genart['GenartMaster']['is_labour_time'] != ConstantsBooleans::ACTIVE) {
            continue;
        }
        ?>
        <tr>
            <td class="ta-left">
                <?php
                echo $genart['Genart']['name_' . $language_code];
                if($genart['Genart']['include_vat']) { ?>
                    <sub class="fw-bold">(<?php echo __t('General.Tax_free'); ?>)</sub>
                    <?php
                }
                ?>
            </td>
            <td class="one ta-left"></td>
            <td class="one ta-left">
                <?php
                if(isset($edit_genarts)) {
					if (!empty($genart['GenartMaster']['min_labour_price']) && !empty($genart['GenartMaster']['max_labour_price'])) {
						?>
						<div class="cnt-range-with-values">
							<div><?php echo $genart['GenartMaster']['min_labour_price']; ?></div>
							<div><?php echo $genart['GenartMaster']['max_labour_price']; ?></div>
							<?php
							echo $this->Form->input(
								'labour_price_works',
								array(
									'type' => 'range',
									'label' => false,
									'min' => $genart['GenartMaster']['min_labour_price'],
									'max' => $genart['GenartMaster']['max_labour_price'],
									'value' => $genart['GarageNetworkGenart']['is_labour_price'] ?? '',
									'step' => isset($genart['GenartMaster']['slider_increment']) ? $genart['GenartMaster']['slider_increment'] : '0.01',
									'oninput' => 'this.parentNode.nextElementSibling.firstChild.value = this.value;this.parentNode.nextElementSibling.nextElementSibling.value = this.value'
							));
							echo $this->Form->input(
								'genart_' . 'is_labour_price' . '.' . $genart['Genart']['id'],
								array(
									'type' => 'number',
									'label' => false,
									'value' => $genart['GarageNetworkGenart']['is_labour_price'] ?? '',
									'style' => 'display:none'
							));
							?>
							<output>
								<?php echo $genart['GarageNetworkGenart']['is_labour_price'] ?? ''; ?>
							</output>
						</div>
					<?php
					} else {
						echo $this->Form->input(
							'genart_' . 'is_labour_price' . '.' . $genart['Genart']['id'],
							array(
								'type' => 'number',
								'label' => false,
								'class' => 'w-15 f-right',
								'value' => $genart['GarageNetworkGenart']['is_labour_price'] ?? '',
								'style' => 'height:20%',
						));
					}
                }
                else
                {
                    if(isset($genart['GarageNetworkGenart']['is_labour_price']) && !empty($genart['GarageNetworkGenart']['is_labour_price'])) {
                        ?>
                        <div style="background-color: #BDECB6">
                            <strong>
                                <?php
                    }
                            if(!empty($genart['GarageNetworkGenart']['is_labour_price'])) { echo $genart['GarageNetworkGenart']['is_labour_price']; }
                            else { echo "-"; }
                    if(!empty($genart['GarageNetworkGenart']['is_labour_price'])) {
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                }
                ?>
            </td>
            <td class="two ta-left"></td>
            <td class="two ta-left">
                <?php
                if(!empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price']) && empty($genart['GarageNetworkGenart']['is_labour_price'])) {
                    ?>
                    <div style="background-color: #BDECB6">
                        <strong>
                            <?php
                }
                if(!empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price'])) { echo $genart['GarageNetworkGenartMaster']['genart_master_labour_price']; }
                else { echo '-'; }
                if(!empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price']) && empty($genart['GarageNetworkGenart']['is_labour_price'])) {
                            ?>
                        </strong>
                    </div>
                    <?php
                }
                ?>
            </td>
            <td class="one ta-left"></td>
            <td class="one ta-left">
                <?php
                if(!empty($genart['GenartMaster']['price_labour_time']) && empty($genart['GarageNetworkGenart']['is_labour_price'])&& empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price'])) {
                    ?>
                    <div style="background-color: #BDECB6">
                        <strong>
                            <?php
                }
                if(!empty($genart['GenartMaster']['price_labour_time'])) { echo $genart['GenartMaster']['price_labour_time']; }
                else { echo '-'; }
                if(!empty($genart['GenartMaster']['price_labour_time']) && empty($genart['GarageNetworkGenart']['is_labour_price'])&& empty($genart['GarageNetworkGenartMaster']['genart_master_labour_price'])) {
                            ?>
                        </strong>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
        <?php
    }
}
?>
