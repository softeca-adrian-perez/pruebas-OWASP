<?php
class LogLoginShell extends Shell
{
    public $uses = array(
        'LogLogin',
    );

    /**
     * Removes incorrect logins that are older than 3 months.
     *
     * console\cake loglogin removeOldIncorrectLogin
     */
    public function removeOldIncorrectLogin()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_incorrect_login - Start", true));
            }
            $sql = '
            DELETE FROM logs_login
            WHERE date < NOW() - INTERVAL 3 MONTH
            AND login = 0;
            ';
            $this->LogLogin->query($sql);
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_incorrect_login - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_incorrect_login - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Removes correct logins that are older than one week.
     *
     * console\cake loglogin removeOldEntries
     */
    public function removeOldEntries()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_login - Start", true));
            }
            $sql = '
            DELETE FROM logs_login
            WHERE date < NOW() - INTERVAL 7 DAY
            AND login = 1;
            ';
            $this->LogLogin->query($sql);
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_login - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Log Login Scheduled Tasks - delete_old_logs_login - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
