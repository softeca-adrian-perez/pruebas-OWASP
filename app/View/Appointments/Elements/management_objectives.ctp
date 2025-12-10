<script src="https://d3js.org/d3-queue.v3.min.js"></script>
<div class="o-auto">
	<table class="table-tracking">
		<thead>
			<tr>
				<th><?php echo __t('Appointment.Objective');?></th>
				<th><?php echo __t('Appointment.Objective_status');?></th>
				<th><?php echo __t('Appointment.Post_visit_comment');?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($management_objectives as $key => $objective) { ?>
				<tr class="manaObjectiveId" data-key="<?php echo $key ?>">
					<td class="c-defecto medium-4">
						<?php
						echo h($objective);

						if(
							!isset($appointment) || (
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED &&
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED )
						){
							echo $this->Form->hidden(
								'Appointment.objectives.'.$key.'.objective_id',
								array(
									'value' => $key,
									'id' => 'objectiveKey'.$key,
								)
							);
						}
						?>
					</td>
					<td class="c-defecto medium-4">
						<?php
						if(
							!isset($appointment) || (
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED &&
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED )
						){
							echo $this->Form->input(
								'Appointment.objectives.'.$key.'.status',
								array(
									'label' => false,
									'type' => 'select',
									'class' => 'select2-multiple',
									'id' => 'objectiveStatus'.$key,
									'options' => $objectives_status,
									'multiple' => false,
									'empty' => false,
									'required' => true,
									'value' => isset($appointment_objectives_management[$key]) ? $appointment_objectives_management[$key]['status'] : 0,
									'style' => 'margin-top: -17px;',
									'disabled' => !isset($appointment) ? 'disabled' : '',
								)
							);
						}
						else{
							echo $objectives_status[$appointment_objectives_management[$key]['status']];
						}
						?>
					</td>
					<td class="c-defecto medium-4">
						<?php
						if(
							!isset($appointment) || (
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED &&
							$appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED )
						){
							echo $this->Form->input(
								'Appointment.objectives.'.$key.'.comment',
								array(
									'label' => false,
									'id' => 'comment'.$key,
									'type' => 'textarea',
									'value' => isset($appointment_objectives_management[$key]) ? $appointment_objectives_management[$key]['comment'] : '',
									'rows' => 1,
									'disabled' => !isset($appointment) ? 'disabled' : '',
								)
							);
						}
						else{
							echo $appointment_objectives_management[$key]['comment'];
						}
						?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
