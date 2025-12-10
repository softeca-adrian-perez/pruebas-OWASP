<div class="p-top-1">
    <b class="fields_views"><?php echo __t('Sendgrid.Viewvars') ?>: </b>
    <br />
    <table>
        <thead>
            <tr>
                <th><?php echo __t('General.Name'); ?></th>
                <th><?php echo __t('General.Value'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($isExternal != ConstantsBooleans::YES) {
                foreach ($sendgrid_email_type_view_vars as $sendgrid_email_type_view_var) {
                    $view_var_value = isset($email_view_vars[$sendgrid_email_type_view_var['SendGridViewVar']['name']]) ? Texto::decryptSendGridViewVarValue($email_type_id, $sendgrid_email_type_view_var['SendGridViewVar']['name'], $email_view_vars[$sendgrid_email_type_view_var['SendGridViewVar']['name']]) : '';
            ?>
                    <tr>
                        <td style='width: 25%' valign="top"><?php echo $sendgrid_email_type_view_var['SendGridViewVar']['name']; ?></td>
                        <td>
                            <?php
                            if (is_array($view_var_value)) {
                                $array_view_var_value = $sendgrid_email_type_view_var['SendGridViewVar']['name'] == 'quotation_jobs' ? $view_var_value[0] : $view_var_value;
                                foreach ($array_view_var_value as $key_value => $value) {
                            ?>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td style='width: 25%' valign="top"><?php echo $key_value; ?></td>
                                                <td>
                                                    <?php
                                                    if (is_array($value)) {
                                                        if (!empty($value)) {
                                                            foreach ($value as $val) {
                                                                foreach ($val as $key_val => $vall) {
                                                    ?>
                                                                    <table>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style='width: 25%'><?php echo $key_val; ?></td>
                                                                                <td><?php echo $vall; ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                <?php
                                                                }
                                                                ?>
                                                                <br />
                                                    <?php
                                                            }
                                                        }
                                                    } else {
                                                        echo $value;
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                            <?php
                                }
                            } else {
                                echo $view_var_value;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <?php foreach ($emailViewVarsKeys as $viewvarKey) { ?>
                    <tr>
                        <td style='width: 25%' valign="top"><?php echo $viewvarKey; ?></td>
                        <td style='width: 25%' valign="top"><?php echo $email_view_vars[$viewvarKey]; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>