<?php
foreach($tutorials as $tutorial)
{
    ?>
    <div class="item-video" data-id="<?php echo $tutorial['Tutorial']['id']; ?>">
        <div class="item-title">
            <?php
            $title = !empty($tutorial['Tutorial']['title']) ? $tutorial['Tutorial']['title'] : __t('Tutorial.No_title');
            if($role_id == ConstantsRoles::ADMIN)
            {
                echo $this->Html->link(
                    $title,
                    array(
                        'controller' => 'user_guides',
                        'action' => 'edit',
                        $tutorial['Tutorial']['id']
                    )
                );
            }
            else
            {
                echo h($title);
            }
            if($role_id == ConstantsRoles::ADMIN)
            {
                echo '<span class="ion-arrow-move"></span>';
            }
            ?>
        </div>
        <div class="embed-container">
            <iframe width="300" height="200" src="<?php echo $tutorial['Tutorial']['url']; ?>"
                    frameborder="0"
                    allowfullscreen="allowfullscreen">
            </iframe>
        </div>
    </div>
    <?php
}
if(isset($load_more) && $load_more <= 12)
{
    ?>
    <div class="columns medium-12 p-0 m-0" style="height: 0">
        <div class="page-load-status p-0 m-0" style="height: 0">
            <div class="loader-ellips infinite-scroll-request p-0 m-0" style="height: 0">
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
            </div>
            <p class="infinite-scroll-last p-0 m-0" style="height: 0"><?php echo __t('Tutorial.End_of_content'); ?></p>
        </div>
    </div>
    <?php
}
?>