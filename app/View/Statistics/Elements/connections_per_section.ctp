<?php $total_sections = 0; ?>
<div class="row p-1">
    <div class="columns large-6 medium-12 m-bottom-1">
        <canvas id="chartSections"></canvas>
    </div>
    <div class="columns large-6 medium-12 m-bottom-1">
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th><?php echo __t('Statistics.Section');?></th>
                        <th><?php echo __t('Statistics.Connections');?></th>
                    </tr>
                </thead>
                <tbody class="d-none">
                    <?php foreach ($users_statistics_sections_names as $key => $section_name){ ?>
                    <tr>
                        <td class="<?php if(is_array($section_name)){ echo 'c-deleted-statistics'; }?>">
                                <?php if(!is_array($section_name)){
                                    echo $section_name;
                                } else {
                                    echo $section_name[0];
                                }?>
                            </td>
                            <td>
                                <?php 
                                echo $users_statistics_sections[$key];
                                $total_sections += $users_statistics_sections[$key];
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="fw-bold"><?php echo __t('General.Total'); ?></td>
                        <td class="fw-bold"><?php echo $total_sections; ?></td>
                    </tr>
                </tbody>    
            </table>
        </div>
    </div>
</div>
