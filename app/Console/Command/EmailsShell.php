<?php
class EmailsShell extends Shell
{
    public $uses = array('Email', 'EmailDaily', 'RequestedChange', 'UserRecoverPassword');

    // console\cake emails sendEmailsBySendgrid
    public function sendEmailsBySendgrid()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - send_emails_by_sendgrid - Start", true));
            }
            $this->Email->sentNotSentSendGridEmails();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - send_emails_by_sendgrid - End", true));
            }
        } catch (Exception $e) {
            CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - send_emails_by_sendgrid - An exception has ocurred " . $e->getMessage(), true));
        }
    }

    // console\cake emails sendNotificationError
    // public function sendNotificationError()
    // {
    //     $this->Email->sendNotificationErrors();
    // }

    // console\cake emails sendEmailDaily
    // public function sendEmailDaily()
    // {
    //     $this->EmailDaily->sendEmailDaily();
    // }

    // console\cake emails requestedChange
    // public function requestedChange()
    // {
    //     $this->RequestedChange->createEmails();
    // }

    // console\cake emails deleteOldEmail
    public function deleteOldEmail()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_emails - Start", true));
            }
            $this->Email->deleteOldEmail();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_emails - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_emails - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake emails deleteRecoverKeys
    public function deleteRecoverKeys()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_recover_keys - Start", true));
            }
            $this->UserRecoverPassword->deleteRecoverKeys();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_recover_keys - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - delete_old_recover_keys - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake emails createBookingReminder
    public function createBookingReminder()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_email - Start", true));
            }
            $this->Email->createBookingReminder();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_email - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_email - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake emails createSecondBookingReminder
    public function createSecondBookingReminder()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_second_email - Start", true));
            }
            $this->Email->createSecondBookingReminder();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_second_email - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - booking_reminder_second_email - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake emails unsentEmails
    function unsentEmails($token) {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - unsent_emails - Start", true));
            }
            $this->Email->getNumberUnsentEmails($token);
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - unsent_emails - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Emails Scheduled Tasks - unsent_emails - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
