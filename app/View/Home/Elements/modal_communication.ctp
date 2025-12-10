<?php
$first_div = 'medium-3';
$second_div = 'medium-9';
if(!$communication['CommunicationSection']['single']){
    $first_div = 'medium-6';
    $second_div = 'medium-6';
}
?>

<div class="columns medium-12">
    <h1 class="ta-center"><?php echo $communication['Communication']['title'];?></h1>
    <h3 class="ta-center"><span><?php echo $communication['Communication']['subtitle'];?></span></h3>
    <div class="columns <?php echo $first_div; ?> ta-center">
        <?php if(isset($communication['Communication']['image'])){ ?>
            <img src="<?php echo FileManager::get_url(ConstantsPath::DIR_COMMUNICATIONS_IMAGE . '/' . $communication['Communication']['image'])?>">
        <?php } ?>
    </div>
    <div class="columns <?php echo $second_div; ?> p-0">
        <div class="columns medium-12 p-0">
            <span class="fw-bold">
                <?php echo h($communication_sections[ $communication['Communication']['communication_section_id'] ]).' -> '.h($sections_subsections[ $communication['Communication']['section_subsection_id'] ]);?>
            </span>
        </div>
        <div class="columns medium-6 p-0">
            <span class="fw-bold">
                <?php echo __t('Communication.Start_date') . ' ' . Fecha::toFormatoVista($communication['Communication']['start_date']);?>
            </span>
        </div>
        <div class="columns medium-6 p-0">
            <span class="c-fallo">
                <?php echo !is_null($communication['Communication']['end_date']) ?
                __t('Communication.End_date') . ' ' .Fecha::toFormatoVista($communication['Communication']['end_date']) :
                __t('Communication.No_end_date');?>
            </span>
        </div>
        <div class="columns medium-12 p-0">
            <?php echo h($communication['Communication']['body']);?>
        </div>

        <?php if(!is_null($communication['Communication']['url'])){ ?>
            <div class="columns medium-12 p-0">
                <br/>
                <a href="<?php echo $communication['Communication']['url'];?>" target="_blank"><?php echo h($communication['Communication']['url']);?></a>
            </div>
        <?php } ?>

        <?php if(!empty($communication_files)){ ?>
        <div class="columns medium-12 p-0">
            <span class="title-in-fieldset"><?php echo __t('General.Files');?></span>
            <?php foreach($communication_files as $key => $file){?>
                <div class="columns medium-12 p-0">
                    <div><?php echo $this->Html->link(
                            $file,
                            array(
                                'controller' => 'communications_files',
                                'action' => 'download_file',
                                $key
                            )
                        );?></div>
                </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>
