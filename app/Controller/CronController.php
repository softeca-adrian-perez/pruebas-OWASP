<?php
set_time_limit(60 * 60); // 60 minutes
ini_set('memory_limit', '1G');

App::uses('AppShell', 'Console/Command');

class CronController extends AppController
{
    public function beforeFilter()
    {
        if (CRON_ACTIVE == '1') {
            $this->Auth->allow();
        } else {
            exit;
        }
        $this->autoRender = $this->layout = null;
    }

    public function booking_reminder_email()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('createBookingReminder');
    }

    public function booking_reminder_second_email()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('createSecondBookingReminder');
    }

    public function create_garages_erp_email_repair()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'RepairMaintenanceShell');

        $job = new RepairMaintenanceShell();
        $job->dispatchMethod('crear_erp_email_talleres');
    }

    public function create_garages_users_repair()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'RepairMaintenanceShell');

        $job = new RepairMaintenanceShell();
        $job->dispatchMethod('crear_usuarios_talleres');
    }

    public function create_new_distributor_objectives()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'SetDistributorObjectiveShell');

        $job = new SetDistributorObjectiveShell();
        $job->dispatchMethod('set_new_objectives');
    }

    public function delete_all_distributor_objectives()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'DeleteAllShell');

        $job = new DeleteAllShell();
        $job->dispatchMethod('delete_all_distributors_objectives');
    }

    public function delete_old_csv_export_files()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'FileShell');

        $job = new FileShell();
        $job->dispatchMethod('removeCsvExportFiles');
    }

    public function delete_old_csv_objectives_files()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'FileShell');

        $job = new FileShell();
        $job->dispatchMethod('removeObjectivesCsvExportFiles');
    }

    public function delete_old_emails()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('deleteOldEmail');
    }

    public function delete_old_logs_apis()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LogApiShell');

        $job = new LogApiShell();
        $job->dispatchMethod('removeOldEntries');
    }

    public function delete_old_logs_incorrect_login()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LogLoginShell');

        $job = new LogLoginShell();
        $job->dispatchMethod('removeOldIncorrectLogin');
    }

    public function delete_old_logs_login()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LogLoginShell');

        $job = new LogLoginShell();
        $job->dispatchMethod('removeOldEntries');
    }

    public function delete_old_logs_shortner_url()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'ShortnerUrlLogShell');

        $job = new ShortnerUrlLogShell();
        $job->dispatchMethod('delete_log_over_30days');
    }

    public function delete_old_quotation_details_pdfs()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LeadgenShell');

        $job = new LeadgenShell();
        $job->dispatchMethod('deleteQuotationDetailsPdfs');
    }

    public function delete_old_recover_keys()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('deleteRecoverKeys');
    }

    public function export_garages_csv_to_sftp()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'ActualizarFTPGarageShell');

        $job = new ActualizarFTPGarageShell();
        $job->dispatchMethod('loadGarageCsv');
        $job->dispatchMethod('sendSftp');
    }

    public function export_uk_database_zip_to_sftp()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'ActualizarFTPGarageShell');

        $job = new ActualizarFTPGarageShell();
        $job->dispatchMethod('loadDatabaseCsv');
        $job->dispatchMethod('zipCreate');
        $job->dispatchMethod('sendSftpDb');
    }

    public function import_uk_distributors_from_sftp()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'UpdatesShell');

        $job = new UpdatesShell();
        $job->dispatchMethod('update_members');
    }

    public function send_emails_by_sendgrid()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('sendEmailsBySendgrid');
    }

    public function send_fleets_repair()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'RepairMaintenanceShell');

        $job = new RepairMaintenanceShell();
        $job->dispatchMethod('send_fleets');
    }

    public function sync_data_from_leadgen()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LeadgenShell');

        $job = new LeadgenShell();
        $job->dispatchMethod('fluidsSync');
        $job->dispatchMethod('worksSync');
        $job->dispatchMethod('workPricesSync');
    }

    public function sync_quotation_pdfs_from_leadgen()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'LeadgenShell');

        $job = new LeadgenShell();
        $job->dispatchMethod('quotationsDetailsPdfSync');
    }

    public function optimize_image_size_with_tiny()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'TinyShell');

        $job = new TinyShell();
        $job->dispatchMethod('optimiseImageSize');
    }

    public function update_kiyoh_and_google_reviews_data()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'GoogleKiyohReviewsShell');

        $job = new GoogleKiyohReviewsShell();
        $job->dispatchMethod('saveGoogleReviews');
        $job->dispatchMethod('saveKiyohReviews');
        $job->dispatchMethod('saveNetworkReviews');
    }

    public function update_feefo_reviews_data()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'FeefoReviewsShell');

        $job = new FeefoReviewsShell();
        $job->dispatchMethod('saveReviews');
        $job->dispatchMethod('saveNetworkReviews');
    }

    public function update_training_actual_allowance()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'TrainingAllowanceShell');

        $job = new TrainingAllowanceShell();
        $job->dispatchMethod('update_actual_allowance');
    }

    public function update_training_allowance_seven_days()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'TrainingAllowanceShell');

        $job = new TrainingAllowanceShell();
        $job->dispatchMethod('update_allowance_7days');
    }

    public function update_bookings_set_expired()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'UpdatesShell');

        $job = new UpdatesShell();
        $job->dispatchMethod('update_bookings_set_expired');
    }

    public function unsent_emails($token)
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'EmailsShell');

        $job = new EmailsShell();
        $job->dispatchMethod('unsentEmails', array($token));
    }

    public function set_email_guid()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'UpdateWebShell');

        $job = new UpdateWebShell();
        $job->dispatchMethod('addGuidToEmail');
    }

    public function set_sendgrid_license_config_guid()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'UpdateWebShell');

        $job = new UpdateWebShell();
        $job->dispatchMethod('addGuidToSendgridLicenseConfig');
    }

    public function set_email_type_guid()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'UpdateWebShell');

        $job = new UpdateWebShell();
        $job->dispatchMethod('addGuidToEmailType');
    }

    public function trainingDeleteOldEntries()
    {
        $this->endConnection();

        App::import('Console/Command', 'AppShell');
        App::import('Console/Command', 'TrainingDeleteOldEntriesShell');

        $job = new TrainingDeleteOldEntriesShell();
        $job->dispatchMethod('deleteOldEntries');
    }

    /**
     * Close the http connection between the server and the client and continues with the scheduled task in background.
     */
    private function endConnection()
    {
        header("Content-Length: 0");
        header('Connection: close');
        flush();
        session_write_close();
        if (is_callable('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
