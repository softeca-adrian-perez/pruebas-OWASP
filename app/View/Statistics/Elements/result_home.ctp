<?php
echo $this->Html->script('/js/garages_users_permissions.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config'); ?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <?php
                if($config[ConstantsConfig::STATISTICS_IP]) {
                    ?> <th> <?php echo __t('UserStatistic.Ip'); ?> </th> <?php
                }
                else {
                    ?>
                    <th> <?php echo __t('User.Name'); ?> </th>
                    <th> <?php echo __t('User.Surname'); ?> </th>
                    <?php
                }
                ?>
                <th> <?php echo __t('UserStatistic.Last_connection'); ?> </th>
                <th> <?php echo __t('UserStatistic.Acceses'); ?> </th>
                <th> <?php echo __t('General.Customer_name'); ?> </th>
                <?php
                foreach($months as $month) {
                    ?> <th class="ta-center"> <?php echo $month; ?> </th> <?php
                }
                ?>
                <th class="ta-center"> <?php echo __t('General.Total'); ?> </th>
            </tr>
        </thead>
        <!-- <tbody class="d-none"> -->
        <tbody>
            <?php
            foreach($users_connections as $user_connection) {
                ?>
                <tr>
                    <?php
                    if($config[ConstantsConfig::STATISTICS_IP]) {
                        ?> <td> <?php echo $user_connection['UserStatistic']['ip']; ?> </td> <?php
                    }
                    else
                    {
                        ?>
                        <td class="<?php if(empty($user_connection['User']['name'])){ echo 'c-deleted-statistics';}?>">
                            <?php
                            if(!empty($user_connection['User']['name'])) { echo $user_connection['User']['name']; }
                            else { echo $user_connection['UserStatistic']['user_name']; }
                            ?>
                        </td>
                        <td>
                            <?php if(!empty($user_connection['User']['surname'])) { echo $user_connection['User']['surname']; } ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td> <?php echo Fecha::toFormatoVista($user_connection['most_recent']); ?> </td>
                    <td> <?php echo $user_connection['access']; ?> </td>
                    <td> <?php echo $user_connection['customer_name']; ?> </td>
                    <?php
                    foreach($months as $key_month => $month) {
                        ?>
                        <td class="ta-center month_connections" data-connection="<?php echo $user_connection[$key_month]; ?>">
                            <?php echo $user_connection[$key_month]; ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td class="ta-center"> <b> <?php echo $user_connection['total']; ?> </b> </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>