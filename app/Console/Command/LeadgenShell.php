<?php
App::uses('Leadgen', 'Lib');

class LeadgenShell extends Shell
{
    /**
     * Sync all the networks linked to Leadgen.
     *
     * console\cake leadgen fluidsSync
     */
    public function fluidsSync()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Fluids - Start", true));
            }
            $leadgen = new Leadgen();
            $leadgen->fluidsSync();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Fluids - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Fluids - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Sync all the works linked to Leadgen.
     *
     * console\cake leadgen worksSync
     */
    public function worksSync()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works - Start", true));
            }
            $leadgen = new Leadgen();
            $leadgen->worksSync();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Sync all the prices of the works linked to Leadgen.
     *
     * console\cake leadgen workPricesSync
     */
    public function workPricesSync()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works Prices - Start", true));
            }
            $leadgen = new Leadgen();
            $leadgen->workPricesSync();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works Prices - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_data_from_leadgen - Works Prices - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Sync the detail PDFs of all the quotations linked to Leadgen.
     *
     * console\cake leadgen quotationsDetailsPdfSync
     */
    public function quotationsDetailsPdfSync()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_quotation_pdfs_from_leadgen - Start", true));
            }
            $leadgen = new Leadgen();
            $leadgen->quotationsDetailsPdfSync();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_quotation_pdfs_from_leadgen - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - sync_quotation_pdfs_from_leadgen - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    /**
     * Deletes all the details PDFs of all the quotations older than 6 months.
     *
     * console\cake leadgen deleteQuotationDetailsPdfs
     */
    public function deleteQuotationDetailsPdfs()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - delete_old_quotation_details_pdfs - Start", true));
            }
            $leadgen = new Leadgen();
            $leadgen->deleteQuotationDetailsPdfs();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - delete_old_quotation_details_pdfs - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Leadgen Scheduled Tasks - delete_old_quotation_details_pdfs - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }
}
