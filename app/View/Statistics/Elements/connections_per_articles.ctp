<div class="row p-1">
    <!-- <div class="columns large-6 medium-12 m-bottom-1">
        <canvas id="chartArticles"></canvas>
    </div> -->
    <div class="columns medium-12 m-bottom-1">
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th><?php echo __t('Statistics.Article');?></th>
                        <th><?php echo __t('Statistics.Connections');?></th>
                    </tr>
                </thead>
                <tbody class="d-none">
                    <?php foreach ($users_statistics_articles_names as $key => $article_name){ ?>
                        <tr>
                            <td class="<?php if(is_array($article_name)){ echo 'c-deleted-statistics'; }?>">
                                <?php if(!is_array($article_name)){
                                    echo $article_name;
                                } else {
                                    echo $article_name[0];
                                }?>
                            </td>
                            <td>
                                <?php echo $users_statistics_articles[$key]; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>    
            </table>
        </div>
    </div>
</div>