<?php
class ActualizarFTPGarageShell extends Shell
{
    public $uses = array('ActualizarFTPGarage');

    // console\cake actualizarftpgarage loadGarageCsv
    public function loadGarageCsv()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Load CSV - Start", true));
            }
            $this->ActualizarFTPGarage->loadGarageCsv();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Load CSV - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Load CSV - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake actualizarftpgarage sendSftp
    public function sendSftp()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Send SFTP - Start", true));
            }
            $this->ActualizarFTPGarage->sendSftp();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Send SFTP - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_garages_csv_to_sftp - Send SFTP - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake actualizarftpgarage loadDatabaseCsv
    public function loadDatabaseCsv()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Load Database CSV - Start", true));
            }
            $this->ActualizarFTPGarage->loadDatabaseCsv();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Load Database CSV - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Load Database CSV - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake actualizarftpgarage zipCreate
    public function zipCreate()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - ZIP Create - Start", true));
            }
            $this->ActualizarFTPGarage->zipCreate();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - ZIP Create - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - ZIP Create - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    // console\cake actualizarftpgarage SendSftpDB
    public function sendSftpDb()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Send SFTP Database - Start", true));
            }
            $this->ActualizarFTPGarage->sendSftpDb();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Send SFTP Database - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Update SFTP Scheduled Tasks - export_uk_database_zip_to_sftp - Send SFTP Database - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
