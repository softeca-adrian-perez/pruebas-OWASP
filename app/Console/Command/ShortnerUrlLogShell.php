<?php
class ShortnerUrlLogShell extends Shell
{
    public $uses = array('ShortnerUrlLog');

    // console\cake shortnerurl delete_log_over_30days
    public function delete_log_over_30days()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Shortner Url Logs Scheduled Tasks - delete_old_logs_shortner_url - Start", true));
            }
            $this->ShortnerUrlLog->deleteAllOver30Days();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Shortner Url Logs Scheduled Tasks - delete_old_logs_shortner_url - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Shortner Url Logs Scheduled Tasks - delete_old_logs_shortner_url - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
