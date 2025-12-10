<?php
class AppointmentsShell extends Shell
{
	public $uses = array(
		'Appointment',
		'DistributorObjective',
	);

	// console\cake appointments appointments_to_pending
	function appointments_to_pending()
	{
		$this->Appointment->appointmentsToPending();
	}

	// console\cake appointments appointments_reminder
	function appointments_reminder()
	{
		$this->Appointment->appointmentsEmailReminder();
	}

	// console\cake appointments delete_distributors_objectives
	function delete_distributors_objectives()
	{
		$this->DistributorObjective->deleteDistributorsObjectives();
	}
}
