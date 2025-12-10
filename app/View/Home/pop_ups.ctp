<?php if (count($pop_ups) > 0) { ?>
    <div class="columns medium-12">
        <div class="owl-carousel owl-theme" id="popup_carousel">
            <?php foreach ($pop_ups as $key => $pop_up) { ?>
                <div class="item">
                    <div class="columns medium-12 p-0">
                        <?php if (count($pop_ups) > 1) { ?>
                            <div class="ta-right">
                                <?php echo $key + 1 . ' / ' . count($pop_ups); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="columns medium-12 p-0">
                        <img src="<?php echo FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $pop_up['Communication']['image']) ?>">
                    </div>
                    <div class="columns medium-12 p-0">
                        <div class="columns medium-4 p-0">
                            <?php
                            if (isset($communication_sections[$pop_up['Communication']['communication_section_id']]) && isset($sections_subsections[$pop_up['Communication']['section_subsection_id']])) {
                                echo $communication_sections[$pop_up['Communication']['communication_section_id']] . ' / ';
                                echo $sections_subsections[$pop_up['Communication']['section_subsection_id']];
                            }
                            ?>
                        </div>
                        <div class="columns medium-12 p-0">
                            <h2>
                                <?php echo $pop_up['Communication']['title']; ?>
                            </h2>
                            <h3>
                                <?php echo $pop_up['Communication']['subtitle']; ?>
                            </h3>
                            <?php
                            $crop_chars = 200;
                            if (strlen($pop_up['Communication']['body']) > $crop_chars) {
                                $link = $this->Html->link(
                                    __t('General.Read_more'),
                                    array(
                                        'controller' => 'communications',
                                        'action' => 'home_section',
                                        $pop_up['Communication']['communication_section_id'],
                                        $pop_up['Communication']['section_subsection_id'],
                                        $pop_up['Communication']['id']
                                    ),
                                    array(
                                        'style' => 'cursor: pointer;'
                                    )
                                );
                                echo h(substr($pop_up['Communication']['body'], 0, $crop_chars)) . '... ' . $link;
                            } else {
                                $link = $this->Html->link(
                                    __t('General.Read_more'),
                                    array(
                                        'controller' => 'communications',
                                        'action' => 'home_section',
                                        $pop_up['Communication']['communication_section_id'],
                                        $pop_up['Communication']['section_subsection_id'],
                                        $pop_up['Communication']['id']
                                    ),
                                    array(
                                        'style' => 'cursor: pointer;'
                                    )
                                );
                                echo h($pop_up['Communication']['body']) . '... ' . $link;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>