<?php
class UpdatesShell extends Shell
{
    public $uses = array('Update');

    //UK data update -  console\cake updates update_members
    public function update_members()
    {
        try {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - import_uk_distributors_from_sftp - Start", true));
            }
            $this->Update->update_members();
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - import_uk_distributors_from_sftp - End", true));
            }
        } catch (Exception $e) {
            if (CRON_DEBUG_ACTIVE) {
                CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - import_uk_distributors_from_sftp - An exception has ocurred " . $e->getMessage(), true));
            }
        }
    }

    //UK data update -  console\cake updates update_bookings_set_expired
    public function update_bookings_set_expired()
        {
            try {
                if (CRON_DEBUG_ACTIVE) {
                    CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - update_bookings_set_expired - Start", true));
                }
                $this->Update->update_bookings_set_expired();
                if (CRON_DEBUG_ACTIVE) {
                    CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - update_bookings_set_expired - End", true));
                }
            } catch (Exception $e) {
                if (CRON_DEBUG_ACTIVE) {
                    CakeLog::write('scheduled_task', print_r("Updates Scheduled Tasks - update_bookings_set_expired - An exception has ocurred " . $e->getMessage(), true));
                }
            }
        }

    //UK data update -  console\cake updates update_garages
    public function update_garages()
    {
        $this->Update->update_garages();
    }

    //Germany data update - console\cake updates update_customers
    public function update_customers()
    {
        $this->Update->update_customers();
    }

    //Germany data update - console\cake updates update_distributors
    public function update_distributors()
    {
        $this->Update->update_distributors();
    }

    //Germany data update - console\cake updates update_profiles
    public function update_profiles()
    {
        $this->Update->update_profiles();
    }

    //Germany data update - console\cake updates update_sales
    public function update_sales()
    {
        $this->Update->update_families();
        $this->Update->update_sales_de_flop_bdm_abs();
        $this->Update->update_sales_de_flop_bdm_per_region_abs();
        $this->Update->update_sales_de_flop_customer_per_bdm_perc();
        $this->Update->update_sales_de_flop_family_abs();
        $this->Update->update_sales_de_per_region();
        $this->Update->update_sales_de_flop_family_per_bdm_abs();
        $this->Update->update_sales_de_per_bdm();
        $this->Update->update_sales_de_flop_family_per_bdm_perc();
        $this->Update->update_sales_de_flop_family_per();
        $this->Update->update_sales_de_flop_customer_per_bdm_abs();
        $this->Update->update_kpi_de();
        $this->Update->update_sales_figures_de();
        $this->Update->update_sales_figures_details_de();
    }

    //FR Reparation data update -  console\cake updates update_rep
    public function update_rep()
    {
        $this->Update->update_rep();
    }

    //FR Distribution data update -  console\cake updates update_suppliers
    public function update_suppliers()
    {
        $this->Update->update_suppliersFR();
    }
}
