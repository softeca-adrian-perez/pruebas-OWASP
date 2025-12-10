<?php $this->SendGridEmailTypeTemplate = ClassRegistry::init('SendGridEmailTypeTemplate'); ?>
<?php $email_types_array = $is_external ? $email_types[$license_order_id][0] : $email_types; ?>
<td colspan="5" data-th="UK">
    <table>
        <thead>
            <tr>
                <th><?php echo __t('Email.Email_type'); ?></th>
                <?php foreach ($languages as $language) { ?>
                    <th width="75"><?php echo $language['Language']['name_' . __l()]; ?></th>
                <?php } ?>
                <th width="155" class="ta-center"><?php echo __t('General.Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($email_types_array as $email_type) {
                $email_type_id = $email_type['EmailType']['id'];
                $email_type_name = $email_type['EmailType']['name_' . __l()];
                $languages_list_email_type = $languages_list;
            ?>
                <tr>
                    <td data-th="<?php echo __t('Email.Email_type'); ?>"><?php echo $email_type_name; ?></td>
                    <?php
                    foreach ($languages as $language) {
                        $language_id = $language['Language']['id'];
                        $language_name = $language['Language']['name_' . __l()];

                        $sendgrid_email_type_template = $this->SendGridEmailTypeTemplate->findByEmailTypeIdAndLanguageIdAndPlatformIdAndCountryIdAndAagRegionId($email_type_id, $language_id, $platform_id, $country_id, $aag_region_id);

                        // if SendGridEmailTypeTamplate is configured for the email type, laguage, country and aag region, the language is unset from langauges_list
                        if ($sendgrid_email_type_template) {
                            unset($languages_list_email_type[$language_id]);
                        }
                    ?>
                        <td data-th="<?php echo $language_name; ?>" class="ta-center">
                            <?php
                            if ($sendgrid_email_type_template) {
                                $sendgrid_email_type_template_id = $sendgrid_email_type_template['SendGridEmailTypeTemplate']['id'];
                            ?>
                                <img class="td-flag cursor-pointer" src="/img/regions/<?php echo $language['Language']['image']; ?>" data-open="edit-sendgrid-template-<?php echo $sendgrid_email_type_template_id ?>" data-is_event="true">
                                <div style="display: none;" id="edit-sendgrid-template-<?php echo $sendgrid_email_type_template_id ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                                    <div class="medium-12 columns p-right-0">
                                        <?php
                                        echo $this->element(
                                            '../Emails/Elements/form_edit_sendgrid_email_type_template',
                                            array(
                                                'email_type_name' => $email_type_name,
                                                'sendgrid_email_type_template' => $sendgrid_email_type_template,
                                                'languages_list' => $languages_list,
                                                'platform_id' => $platform_id,
                                                'is_web_language' => false
                                            )
                                        );
                                        ?>
                                    </div>
                                    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                                </div>
                            <?php
                            } else {
                                // if SendGridEmailTypeTemplate isn't configured for the email type, laguage, country and aag region, flag is displayed as inactive
                            ?>
                                <img class="td-flag inactive" src="/img/regions/<?php echo $language['Language']['image']; ?>">
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td data-th="<?php echo __t('General.Actions'); ?>" class="ta-center">
                        <span data-open='add-sendgrid-template-<?php echo $email_type_id . '-' . $country_id; ?>' data-is_event="true">
                            <span class="icon cursor-pointer ion-android-add-circle color-green-btn"></span>
                        </span>
                        <div style="display: none;" id='add-sendgrid-template-<?php echo $email_type_id . '-' . $country_id; ?>' class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                            <div class="medium-12 columns p-right-0">
                                <?php
                                echo $this->element(
                                    '../Emails/Elements/form_add_sendgrid_email_type_template',
                                    array(
                                        'email_type_name' => $email_type_name,
                                        'languages_list_email_type' => $languages_list_email_type,
                                        'email_type_id' => $email_type_id,
                                        'platform_id' => $platform_id,
                                        'country_id' => $country_id,
                                        'aag_region_id' => $aag_region_id,
                                        'is_web_language' => false
                                    )
                                );
                                ?>
                            </div>
                            <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php 
    if (!empty($languages_web_list_and_flags)) { ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <?php foreach ($languages_web_list_and_flags as $language) { ?>
                        <th width="75"><?php echo $language['LanguageWebNetwork']['name']; ?></th>
                    <?php } ?>
                    <th width="155" class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($email_types_webs_languages as $email_type) {
                    $email_type_id = $email_type['EmailType']['id'];
                    $email_type_name = $email_type['EmailType']['name_' . __l()];
                    $languages_list_email_type = $languages_web_list;
                ?>
                    <tr>
                        <td data-th="<?php echo __t('Email.Email_type'); ?>"><?php echo $email_type_name; ?></td>
                        <?php
                        foreach ($languages_web_list_and_flags as $language) {
                            $language_id = $language['LanguageWebNetwork']['id'];
                            $language_name = $language['LanguageWebNetwork']['name'];

                            $sendgrid_email_type_template = $this->SendGridEmailTypeTemplate->findByEmailTypeIdAndLanguageWebIdAndPlatformIdAndCountryIdAndAagRegionId($email_type_id, $language_id, $platform_id, $country_id, $aag_region_id);
                            // if SendGridEmailTypeTamplate is configured for the email type, weblaguage, country and aag region, the language is unset from langauges_list
                            if ($sendgrid_email_type_template) {
                                unset($languages_list_email_type[$language_id]);
                            }
                        ?>
                            <td data-th="<?php echo $language_name; ?>" class="ta-center">
                                <?php
                                if ($sendgrid_email_type_template) {
                                    $sendgrid_email_type_template_id = $sendgrid_email_type_template['SendGridEmailTypeTemplate']['id'];
                                ?>
                                    <img class="td-flag cursor-pointer" src="/img/flags/<?php echo $language['LanguageWebFlag']['url']; ?>" data-open="edit-sendgrid-template-<?php echo $sendgrid_email_type_template_id ?>" data-is_event="true">
                                    <div style="display: none;" id="edit-sendgrid-template-<?php echo $sendgrid_email_type_template_id ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                                        <div class="medium-12 columns p-right-0">
                                            <?php
                                            echo $this->element(
                                                '../Emails/Elements/form_edit_sendgrid_email_type_template',
                                                array(
                                                    'email_type_name' => $email_type_name,
                                                    'sendgrid_email_type_template' => $sendgrid_email_type_template,
                                                    'languages_list' => $languages_web_list,
                                                    'platform_id' => $platform_id,
                                                    'is_web_language' => true
                                                )
                                            );
                                            ?>
                                        </div>
                                        <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                                    </div>
                                <?php
                                } else {
                                    // if SendGridEmailTypeTemplate isn't configured for the email type, laguage, country and aag region, flag is displayed as inactive
                                ?>
                                    <img class="td-flag inactive" src="/img/flags/<?php echo $language['LanguageWebFlag']['url']; ?>">
                                <?php } ?>
                            </td>
                        <?php } ?>
                        <td data-th="<?php echo __t('General.Actions'); ?>" class="ta-center">
                            <span data-open='add-sendgrid-template-<?php echo $email_type_id . '-' . $country_id; ?>' data-is_event="true">
                                <span class="icon cursor-pointer ion-android-add-circle color-green-btn"></span>
                            </span>
                            <div style="display: none;" id='add-sendgrid-template-<?php echo $email_type_id . '-' . $country_id; ?>' class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                                <div class="medium-12 columns p-right-0">
                                    <?php
                                    echo $this->element(
                                        '../Emails/Elements/form_add_sendgrid_email_type_template',
                                        array(
                                            'email_type_name' => $email_type_name,
                                            'languages_list_email_type' => $languages_list_email_type,
                                            'email_type_id' => $email_type_id,
                                            'platform_id' => $platform_id,
                                            'country_id' => $country_id,
                                            'aag_region_id' => $aag_region_id,
                                            'is_web_language' => true
                                        )
                                    );
                                    ?>
                                </div>
                                <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</td>