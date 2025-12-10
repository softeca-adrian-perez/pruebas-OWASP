<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Communication.Communications'),
                array(
                    'controller' => 'home',
                    'action' => 'home_page2'
                )
            ),
            __t('Communication.Section'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../Home/Elements/home_bullets'); ?>
<div class="cnt-data fg-0">
    <div class="header-articles">
        <?php echo $this->element('../Home/Elements/home_header'); ?>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <?php
        $active = '';
        if( !$subsection_id )
        {
            $active = 'active';
        }
        ?>
        <li class="<?php echo $active; ?>">
            <?php
            echo $this->Html->link(
                __t('General.All'),
                array(
                    'controller' => 'communications',
                    'action' => 'home_section',
                    $section_id,
                    null,
                    null
                )
            );
            ?>
        </li>
        <?php
        foreach($subsections as $subsection)
        {
            $active = '';
            if( $subsection_id == $subsection['SectionSubsection']['id'] )
            {
                $active = 'active';
            }
            ?>
            <li class="<?php echo $active; ?>">
                <?php
                echo $this->Html->link(
                    $subsection['SectionSubsection']['name' . __s()],
                    array(
                        'controller' => 'communications',
                        'action' => 'home_section',
                        $section_id,
                        $subsection['SectionSubsection']['id'],
                        null
                    )
                );
                ?>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php echo h($section['CommunicationSection']['name'.__s()]); ?>
        </div>
        <?php
        if($communication)
        {
            ?>
            <div class="row">
                <?php if (!empty(trim($communication['Communication']['url']))) { ?>
                    <a href=" <?php echo $communication['Communication']['url']; ?>">
                <?php } ?>
                    <div class="medium-3 columns cnt-img-communication p-0">
                            <span>
                                <?php echo $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication['Communication']['image']),array('class' => 'img-communication',) ); ?>
                            </span>
                    </div>
                <?php if (!empty(trim($communication['Communication']['url']))) { ?>
                    </a>
                <?php } ?>
                <div class="medium-9 columns">
                    <div class="d-inline-block w-100p">
                        <h2>
                            <?php echo h($communication['Communication']['title']); ?>
                        </h2>
                    </div>
                    <div class="d-inline-block w-100p fs-large">
                        <?php echo h($communication['Communication']['subtitle']); ?>
                    </div>
                    <div class="d-inline-block w-100p p-vertical-1">
                        <?php echo h($communication['Communication']['body']); ?>
                    </div>
                    <?php
                    if(!is_null($communication['Communication']['url']))
                    {
                        ?>
                        <div class="d-inline-block w-100p p-bottom-1">
                            <a href="<?php echo h($communication['Communication']['url']);?>" target="_blank"><?php echo h($communication['Communication']['url']);?></a>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="d-inline-block w-100p">
                        <?php echo __t('Communication.Start_date') . ': <strong>' . Fecha::toFormatoVista($communication['Communication']['start_date']).'</strong>'; ?>
                            -
                        <span class="c-fallo">
                            <?php echo !is_null($communication['Communication']['end_date']) ?
                            __t('Communication.End_date') . ': ' .Fecha::toFormatoVista($communication['Communication']['end_date']) :
                            __t('Communication.No_end_date');?>
                        </span>
                    </div>
                    <?php
                    if($communication_files)
                    {
                        ?>
                        <div class="d-inline-block w-100p p-top-1">
                            <span class="title-in-fieldset"><?php echo __t('General.Files');?></span>
                            <?php
                            foreach($communication_files as $key => $file)
                            {
                                ?>
                                <div>
                                    <?php
                                    echo $this->Html->link(
                                        $file['CommunicationFile']['source_name'],
                                        array(
                                            'controller' => 'communications_files',
                                            'action' => 'download_file',
                                            $file['CommunicationFile']['id']
                                        )
                                    );
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        if(!$communication && $subsection_id)
        {
            $count=0;
            foreach($communications_subsection as $key => $communication_subsection)
            {
                ?>
                <div class="columns medium-3 p-1 end <?php if( $count%4==0 ){ echo 'clear';}?>" >
                    <div class="cnt-communication-item">
                        <div class="columns medium-12 ta-center text-communication-title">
                            <?php echo h($communication_subsection['Communication']['title']); ?>
                        </div>
                        <div class="columns medium-6 ta-center text-communication-date">
                            <?php echo Fecha::toFormatoVista(date('Y-m-d',strtotime($communication_subsection['Communication']['start_date']))); ?>
                        </div>
                        <div class="columns medium-6 ta-center text-communication-subcategory">
                            <?php echo h($sections_list[$communication_subsection['Communication']['section_subsection_id']]); ?>
                        </div>
                        <div class="columns medium-12 p-0 cnt-image-communication">
                            <?php
                            echo $this->Html->link(
                                $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication_subsection['Communication']['image']) ),
                                array(
                                    'controller' => 'communications',
                                    'action' => 'home_section',
                                    $communication_subsection['Communication']['communication_section_id'],
                                    $communication_subsection['Communication']['section_subsection_id'],
                                    $communication_subsection['Communication']['id']
                                ),
                                array(
                                    'escape' => false,
                                )
                            );
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $count++;
            }
        }
        if(!$communication && !$subsection_id)
        {
            $count=0;
            if($section['CommunicationSection']['visual'])
            {
                ?>
                <div class="cnt-communication-items">
                    <div class="row">
                        <?php
                        foreach($last_communications_by_subsection as $key => $last_communication_by_subsection)
                        {
                            if(isset($last_communication_by_subsection['Communication']['Communication']))
                            {
                                ?>
                                <div class="columns medium-3 end <?php if( $count%4==0 ){ echo 'clear p-left-0';}?>" >
                                    <div class="cnt-communication-item">
                                        <div class="columns medium-12 ta-center text-communication-title">
                                            <?php echo h($last_communication_by_subsection['Communication']['Communication']['title']); ?>
                                        </div>
                                        <div class="columns medium-6 ta-center text-communication-date">
                                            <?php echo Fecha::toFormatoVista(date('Y-m-d',strtotime($last_communication_by_subsection['Communication']['Communication']['start_date']))); ?>
                                        </div>
                                        <div class="columns medium-6 ta-center text-communication-subcategory">
                                            <?php echo h($last_communication_by_subsection['Section']['SectionSubsection']['name'.__s()]); ?>
                                        </div>
                                        <div class="columns medium-12 p-0 cnt-image-communication">
                                            <?php
                                            echo $this->Html->link(
                                                $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $last_communication_by_subsection['Communication']['Communication']['image']) ),
                                                array(
                                                    'controller' => 'communications',
                                                    'action' => 'home_section',
                                                    $last_communication_by_subsection['Communication']['Communication']['communication_section_id'],
                                                    $last_communication_by_subsection['Communication']['Communication']['section_subsection_id'],
                                                    $last_communication_by_subsection['Communication']['Communication']['id']
                                                ),
                                                array('escape' => false)
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $count++;
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            else
            {
                foreach($subsections as $key => $subsections)
                {
                    ?>
                    <div class="columns medium-3 p-1 end <?php if( $count%4==0 ){ echo 'clear';}?>" >
                        <div class="columns medium-12 ta-center text-communication-title">
                            <?php echo h($subsections['SectionSubsection']['name'.__s()]); ?>
                        </div>
                        <div class="columns medium-12 p-0">
                            <?php
                            echo $this->Html->link(
                                $this->Html->image(FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $subsections['SectionSubsection']['image']) ),
                                array(
                                    'controller' => 'communications',
                                    'action' => 'home_section',
                                    $section_id,
                                    $subsections['SectionSubsection']['id'],
                                    null
                                ),
                                array('escape' => false)
                            );
                            ?>
                        </div>
                    </div>
                    <?php
                    $count++;
                }
            }
        }
        ?>
    </div>
</div>