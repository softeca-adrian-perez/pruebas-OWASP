		<style>
			* {
				font-family: "MarkPro", sans-serif;
				font-size: 1em;
				color: #565353;
				margin: 0;
				padding: 0;
				background: none;
			}

			tr:nth-child(even) {
				background-color: #c9cccf
			}

			th {
				background-color: #0c88e5;
				color: white;
			}

			.left {
				float: left;
				width: 48%;
			}

			.right {
				float: right;
				width: 48%;
			}

			div img {
				display: block;
				margin: auto;
			}
		</style>
		<?php

		$networks = CakeSession::read('Auth.User.networks');

		echo $this->Html->css('estilos.css?v=' . Configure::read('VERSION_CACHE'), array('id' => 'css_id'));
		?>
		<div class="row" data-equalizer>
			<div class="columns medium-6" data-equalizer-watch>
				<fieldset class="columns fieldset-garage-list">
					<div class="columns medium-12 p-top-1 title-in-fieldset">
						<?php
						if (isset($appointment_created_by)) {
							echo __t('Appointment.Created_by') . ': ' . $appointment_created_by['User']['full_name'];
						}
						?>
					</div>
					<div class="medium-12 columns p-right-0">
						<label class="fields_views"><?php echo __t('Appointment.Customer'); ?></label>
					</div>
					<div class="medium-12 columns p-right-0">
						<?php
						if ($garage) { ?>
							<h4 class="p-bottom-1">
								<?php echo __t('Appointment.Garage_name') . ' : ' . $garage['Garage']['name'] . ' - ' . $garage['Garage']['g_number_id'] . ' - ' . $garage['Garage']['town']; ?>
							</h4>
						<?php }
						if ($distributor) { ?>
							<h4 class="p-bottom-1">
								<?php echo __t('Appointment.Distributor_name') . ' : ' . $distributor['Distributor']['name'] . ' - ' . $distributor['Distributor']['account_number'] . ' - ' . $distributor['Distributor']['town']; ?>
							</h4>
						<?php }
						?>
					</div>
					<div class="medium-12 columns p-right-0">
						<b><?php echo __t('Appointment.Assign_to') . ': '; ?></b>
						<?php echo $users[$appointment['Appointment']['user_assigned_id']]; ?>
					</div>
					<div class="medium-12 columns">
						<b><?php echo __t('Appointment.Date') . ': '; ?></b>
						<?php echo Fecha::toFormatoVistaFecha($appointment['Appointment']['date']); ?>
						<b><?php echo __t('Appointment.Start_time') . ': '; ?></b>
						<?php echo $appointment['Appointment']['start_time']; ?>
						<b><?php echo __t('Appointment.End_time') . ': '; ?></b>
						<?php echo $appointment['Appointment']['end_time']; ?>
					</div>

					<div class="medium-12 columns p-0">
						<div class="medium-4 columns">
							<b><?php echo __t('General.Status') . ': '; ?></b>
							<?php echo $appointment_status[$appointment['Appointment']['appointment_status_id']]; ?>
						</div>
						<div class="medium-4 columns end">
							<b><?php echo __t('Appointment.Type') . ': '; ?></b>
							<?php echo $appointment_types[$appointment['Appointment']['appointment_type_id']]; ?>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="columns medium-6" data-equalizer-watch>
				<fieldset class="columns fieldset-garage-list">
					<?php if (!empty($distributor)) { ?>
						<div class="columns medium-12 p-left-1">
							<b><?php echo __t('Distributor.Trading_group') . ': '; ?></b>
							<?php if (isset($distributor['TradingGroup']) && !empty($distributor['TradingGroup'])) { ?>
								<img title="<?php echo h($distributor['TradingGroup']['name']); ?>" src="<?php echo FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $distributor['TradingGroup']['image']; ?>" style="max-width: 100px">
								<?php echo h($distributor['TradingGroup']['name']); ?>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="columns medium-12 p-0 p-left-1">
						<b><?php echo __t('Sales.Latest_visit') . ':' ?></b>
						<?php if (isset($garage['Garage']['last_visit'])) {
							echo ($garage['Garage']['last_visit']) ? Fecha::toFormatoVistaFecha($garage['Garage']['last_visit']) : null;
						} elseif (isset($distributor['Distributor']['last_visit'])) {
							echo ($distributor['Distributor']['last_visit']) ? Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']) : null;
						} ?>
					</div>
					<?php if (!empty($distributor)) { ?>
						<div class="columns medium-12">
							<b><?php echo __t('Appointment.Visit_count_ltm') . ':' ?></b>
							<?php echo isset($ltm) ? $ltm : ''; ?>
						</div>
						<div class="medium-12 columns">
							<b><?php echo __t('Sales.Figures'); ?></b>
							<div class="medium-12 columns">
								<b><?php echo 'MTD +/-' . ': '; ?></b>
								<?php echo $appointment['Appointment']['mtd']; ?>
								<b><?php echo 'QTD +/-' . ': '; ?></b>
								<?php echo $appointment['Appointment']['qtd']; ?>
								<b><?php echo 'YTD +/-' . ': '; ?></b>
								<?php echo $appointment['Appointment']['ytd']; ?>
							</div>
						</div>
					<?php } ?>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="columns medium-12">
				<fieldset class="columns fieldset-garage-list">
					<div class="medium-12 columns">
						<b class="fs-medium" style="padding-bottom: .5rem;">
							<?php echo __t('Appointment.Visit_objectives'); ?>
						</b>
						<hr class="m-top-0">
					</div>
					<div class="medium-12 columns">
						<b>
							<?php echo __t('Appointment.Customer_performance_summary') . ': '; ?></br>
						</b>
						<p>
							<?php echo $appointment['Appointment']['customer_performance_summary']; ?>
						</p>
					</div>

					<div class="medium-12 columns p-bottom-1">
						<b>
							<?php echo __t('Appointment.Management_objectives'); ?>
							<b>
								<div class="o-auto">
									<table>
										<thead>
											<tr>
												<th><?php echo __t('Appointment.Objective'); ?></th>
												<th><?php echo __t('Appointment.Objective_status'); ?></th>
												<th><?php echo __t('Appointment.Post_visit_comment'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($management_objectives as $key => $objective) { ?>
												<tr>
													<td class="c-defecto">
														<?php echo h($objective); ?>
													</td>
													<td class="c-defecto">
														<?php echo h($objectives_status[$appointment_objectives_type_management[$key]['status']]); ?>
													</td>
													<td class="c-defecto">
														<?php echo h($appointment_objectives_type_management[$key]['comment']); ?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
					</div>

					<div class="medium-12 columns">
						<b>
							<?php echo __t('Appointment.Personal_objectives'); ?>
						</b>
						<div class="o-auto">
							<table>
								<thead>
									<tr>
										<th><?php echo __t('Appointment.Objective'); ?></th>
										<th><?php echo __t('Appointment.Objective_status'); ?></th>
										<th><?php echo __t('Appointment.Post_visit_comment'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($personal_objectives as $key => $objective) { ?>
										<tr>
											<td class="c-defecto">
												<?php echo h(isset($objective) ? $objective : null); ?>
											</td>
											<td class="c-defecto">
												<?php echo h(isset($objectives_status[$appointment_objectives_type_personal[$key]['status']]) ? $objectives_status[$appointment_objectives_type_personal[$key]['status']] : null); ?>
											</td>
											<td class="c-defecto">
												<?php echo h(isset($appointment_objectives_type_personal[$key]['comment']) ? $appointment_objectives_type_personal[$key]['comment'] : null); ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="columns medium-12">
				<fieldset class="columns fieldset-garage-list">
					<div class="columns medium-12">
						<b>
							<?php echo __t('Appointment.Feedback') . ': '; ?></br>
						</b>
						<p>
							<?php echo $appointment['Appointment']['feedback']; ?>
						</p>
						<b>
							<?php echo __t('Appointment.Notify_to') . ' ' . __t('Task.Contact_lists') . ': '; ?></br>
						</b>
						<p>
							<?php
							foreach ($appointment['Appointment']['appointment_contact_lists'] as $key) {
								echo $contact_lists[$key] . '</br>';
							}
							?>
						</p>
					</div>
					<div class="columns medium-12">
						<b>
							<?php echo __t('Appointment.Feeling') . ': '; ?></br>
						</b>
						<div class="d-inline-block cont-radio-image w-100p p-vertical-1 cont-radio p-left-1" style="padding-top: .5rem;">
							<?php foreach ($appointment_feelings as $feeling) { ?>
								<?php if ($feeling['AppointmentFeeling']['id'] == $appointment['Appointment']['appointment_feeling_id']) { ?>
									<div class="appointment_feelings">
										<label style="color: <?php echo $feeling['AppointmentFeeling']['color'];  ?>; !important">
											<?php echo $this->Html->image(FilePaths::ICONS_IMAGES_RELATIVE . $feeling['AppointmentFeeling']['icon'], array('style' => 'padding: 0; max-width: 35px;')); ?>
											<?php echo $feeling['AppointmentFeeling']['name' . __s()]; ?>
										</label>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
					<div class="medium-12 columns f-left cont-services follow_up">
						<b>
							<?php echo __t('Appointment.Requires_follow_up') . ': '; ?>
						</b>
						<?php if ($appointment['Appointment']['requires_follow_up']) { ?>
							<?php echo  Booleano::toString(true); ?>
						<?php } else { ?>
							<?php echo  Booleano::toString(false); ?>
						<?php } ?>
					</div>
				</fieldset>
			</div>

			<div class="columns medium-12">
				<fieldset class="columns fieldset-garage-list">
					<div class="columns medium-12">
						<b>
							<?php echo __t('Appointment.Comments'); ?>
						</b>
						<?php foreach ($comments as $comment) { ?>
							<div class="columns medium-12">
								<div class="d-inline-block w-100p">
									<span class="autor-comentario f-left"><?php echo $users[$comment['AppointmentComment']['user_id']]; ?></span>
									<span class="fecha-comentario f-right"><?php echo $comment['AppointmentComment']['creation_date']; ?></span>
								</div>
								<div class="columns medium-12 fieldset-comments">
									<span class="tip tip-left"></span>
									<p>
										<?php echo nl2br($comment['AppointmentComment']['body']); ?>
									</p>
								</div>
							</div>
						<?php } ?>
					</div>
				</fieldset>
			</div>
		</div>