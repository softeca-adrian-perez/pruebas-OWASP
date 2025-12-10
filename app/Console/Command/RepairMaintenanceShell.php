<?php
class RepairMaintenanceShell extends Shell
{
    public $uses = array('RepairMaintenance', 'Garage');

    // Console\cake RepairMaintenance crear_usuarios_talleres
    public function crear_usuarios_talleres()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_users_repair - Start", true));
            }
            $this->RepairMaintenance->crear_usuarios_talleres_repairmaintenance();
            $this->RepairMaintenance->crear_usuarios_repairmaintenance();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_users_repair - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_users_repair - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // Console\cake RepairMaintenance crear_erp_email_talleres
    public function crear_erp_email_talleres()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_erp_email_repair - Start", true));
            }
            $this->Garage->crear_erp_email_talleres();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_erp_email_repair - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - create_garages_erp_email_repair - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // Console\cake RepairMaintenance send_fleets
    public function send_fleets()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - send_fleets_repair - Start", true));
            }
            $this->RepairMaintenance->sendFleetsToRepair();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - send_fleets_repair - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Repair Maintenence Scheduled Tasks - send_fleets_repair - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
