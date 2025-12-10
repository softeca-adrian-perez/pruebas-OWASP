<div class="p-vertical-1 flex">
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.customer_name">
		<?php echo __t('General.Customer_name'); ?>
	</span>
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.customer_email">
		<?php echo __t('Garage.Customer_email'); ?>
	</span>
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.customer_phone">
		<?php echo __t('General.Customer_phone'); ?>
	</span>
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.garage_name">
		<?php echo __t('Appointment.Garage_name'); ?>
	</span>
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.date">
		<?php echo __t('Garage.Date'); ?>
	</span>
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.time">
		<?php echo __t('General.Time'); ?>
	</span>
	<?php if ($template_type_id == ConstantsSmsTemplateTypes::BOOKING) { ?>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.Time_to">
			<?php echo __t('General.Time_to'); ?>
		</span>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.plate">
			<?php echo __t('General.Plate'); ?>
		</span>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.booking_id">
			<?php echo __t('Booking.Booking_id'); ?>
		</span>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.network_name">
			<?php echo __t('Training.Network_name'); ?>
		</span>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.work_name">
			<?php echo __t('General.Work') . ' ' . __t('General.Name'); ?>
		</span>
	<?php } elseif ($template_type_id == ConstantsSmsTemplateTypes::ENQUIRY) { ?>
		<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.enquiry_id">
			<?php echo __t('Enquiries.Enquiry_ID'); ?>
		</span>
	<?php } ?>
</div>
<div class="p-vertical-1 flex">
	<span class="aag-button small <?php echo isset($class) ? $class : 'button-sms-js';?>" data-value="$description.url">
		<?php echo __t('Website.Url'); ?>
	</span>
</div>