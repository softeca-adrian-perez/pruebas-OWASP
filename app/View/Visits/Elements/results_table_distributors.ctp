<div class="medium-12 columns p-0">
    <div class="o-auto">
        <table id="visit-table" class="table-tracking tabla-responsive">
            <thead>
            <tr>
                <th><input type="checkbox" id="mark_unmark_all"></th>
                <th><?php echo __t('Distributor.Account_number');?></th>
                <th><?php echo __t('Appointment.Customer'); ?></th>
                <th><?php echo __t('Distributor.Postcode');?></th>
                <th><?php echo __t('Visit.City');?></th>
                <th class="ta-center"><?php echo __t('Visit.Last_visit');?></th>
                <th class="d-none"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $three_months = date('Y-m-d', strtotime("-3 months"));
            $two_weeks = date('Y-m-d', strtotime("-2 weeks"));
            foreach($distributors as $key => $distributor)
            {
                echo $this->element('../Visits/Elements/row_distributor',
                    array(
                        'key' => $key,
                        'distributor' => $distributor,
                        'three_months' => $three_months,
                        'two_weeks' => $two_weeks
                    )
                );
            }
            ?>
            </tbody>
        </table>
    </div>
</div>