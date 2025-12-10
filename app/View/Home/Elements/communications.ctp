<div class="cnt-home_page2 aag-margin">
    <div>
        <?php
        if ($categories_communications) {
            foreach($categories_communications as $category_communications)
            {
                if(isset($category_communications['Communications']) && $category_communications['Communications'])
                {
                    if($category_communications['Scrolling'])
                    {
                        ?>
                        <div class="cnt-item-with-image">
                            <span class="title-carousel"><?php echo h($category_communications['Section']); ?></span>
                            <div class="owl-carousel owl-theme sections_carousel">
                                <?php
                                foreach($category_communications['Communications'] as $communication)
                                {
                                    ?>
                                    <div class="item">
                                        <?php
                                        echo $this->Html->link(
                                            $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication['Communication']['image']) ),
                                            array(
                                                'controller' => 'communications',
                                                'action' => 'home_section',
                                                $communication['Communication']['communication_section_id'],
                                                $communication['Communication']['section_subsection_id'],
                                                $communication['Communication']['id']
                                            ),
                                            array(
                                                'class' => 'cnt-img-carousel',
                                                'escape' => false,
                                            )
                                        );
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    if(!$category_communications['Scrolling'])
                    {
                        ?>
                        <div class="cnt-item-with-image">
                            <span class="title-carousel"><?php echo h($category_communications['Section']); ?></span>
                            <div class="item">
                                <?php
                                echo $this->Html->link(
                                    $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $category_communications['SectionImage']) ),
                                    array(
                                        'controller' => 'communications',
                                        'action' => 'home_section',
                                        $category_communications['Id'],
                                        null,
                                        null
                                    ),
                                    array(
                                        'class' => 'cnt-img-carousel',
                                        'escape' => false,
                                    )
                                );
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }
        ?>
    </div>
    <div>
        <?php
        if(isset($last_communications) && $last_communications)
        {
            ?>
            <div class="cnt-item-with-image">
                <span class="title-carousel"><?php echo __t('Communication.Last_news'); ?></span>
                <div class="o-auto">
                    <table class="table-last-news">
                        <thead class="d-none">
                            <tr>
                                <th><?php echo __t('Communication.Start_date'); ?></th>
                                <th><?php echo __t('Communication.Title'); ?></th>
                                <th><?php echo __t('Communication.Section'); ?></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($last_communications as $new)
                            {
                                ?>
                                <tr>
                                    <td class="ws-nowrap">
                                        <?php echo Fecha::toFormatoVista($new['Communication']['start_date']);?>
                                    </td>
                                    <td class="c-primary">
                                        <?php
                                        echo $this->Html->link(
                                            $new['Communication']['title'],
                                            array(
                                                'controller' => 'communications',
                                                'action' => 'home_section',
                                                $new['Communication']['communication_section_id'],
                                                $new['Communication']['section_subsection_id'],
                                                $new['Communication']['id']
                                            ),
                                            array(
                                            )
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo h($communication_sections[$new['Communication']['communication_section_id']]); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>