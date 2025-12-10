<?php
class LogApiShell extends Shell
{
    public $uses = array(
        'LogApi',
    );

    /**
     * Removes entries that are older than a week.
     *
     * console\cake log_api removeOldEntries
     *
     * T001 SECURITY - It is not changed because it does not receive variable parameters per call.
     */
    public function removeOldEntries()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Api Scheduled Tasks - delete_old_logs_apis - Start", true));
            }
            $sql = '
            DELETE FROM logs_apis
            WHERE date < NOW() - INTERVAL 7 DAY;
            ';
            $this->LogApi->query($sql);
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Api Scheduled Tasks - delete_old_logs_apis - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Api Scheduled Tasks - delete_old_logs_apis - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
