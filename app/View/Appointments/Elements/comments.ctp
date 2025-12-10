<?php
foreach ($comments as $comment) {
?>
    <div class="columns medium-2 clear ta-center p-top-1">
        <div class="cnt-avatar-usuario">
            <?php
            $image = false;
            foreach ($users_images as $user_image) {
                if ($comment['AppointmentComment']['user_id'] == $user_image['UserImage']['user_id']) {
                    echo $this->Html->image(
                        Router::url(
                            array(
                                'controller' => 'users_images',
                                'action' => 'download_file',
                                'plugin' => false,
                                $user_image['UserImage']['id'],
                            )
                        ),
                        array(
                            'alt' => '',
                            'class' => "img_comment_small",
                        )
                    );
                    $image = true;
                }
            }
            if ((empty($users_images) || !$image) && isset($users[$comment['AppointmentComment']['user_id']])) {
                $tmp = explode(" ", $users[$comment['AppointmentComment']['user_id']]);
                $first = mb_substr($tmp[0], 0, 1, 'UTF-8');
                $second = mb_substr($tmp[1], 0, 1, 'UTF-8');
                echo $first . $second;
            }
            ?>
        </div>
    </div>
    <div class="columns medium-10 p-top-1">
        <div class="d-inline-block w-100p">
            <span class="autor-comentario f-left"><?php echo isset($users[$comment['AppointmentComment']['user_id']]) ? $users[$comment['AppointmentComment']['user_id']] : ""; ?></span>
            <span class="fecha-comentario f-right"><?php echo $comment['AppointmentComment']['creation_date']; ?></span>
        </div>
        <div class="columns medium-12 fieldset-comments">
            <span class="tip tip-left"></span>
            <p>
                <?php echo nl2br($comment['AppointmentComment']['body']); ?>
            </p>
        </div>
    </div>
<?php
}
?>
<div class="load-more ta-center">
    <?php
    $class = '';
    if ($load_more == ConstantsBooleans::NO) {
        $class = 'd-none';
    }
    ?>
    <button type="button" class="aag-button three medium outlined <?php echo $class ?>" id="load-more-comments" data-start="<?php echo $start ?>" data-url="<?php echo Router::url(
                                                                                                                                                                array(
                                                                                                                                                                    'controller' => 'appointments_comments',
                                                                                                                                                                    'action' => 'ajax_load_more'
                                                                                                                                                                )
                                                                                                                                                            ); ?>">
        <?php echo __t('General.Load_more'); ?>
        <span class="ion-refresh"></span>
    </button>
</div>