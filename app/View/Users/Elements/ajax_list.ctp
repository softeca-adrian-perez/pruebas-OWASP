<div id="<?php echo $id ?>" class="m-top-1">
    <?php foreach ($users as $user) { ?>
        <div class="ui-state-highlight" data-id="<?php echo $user['User']['id'] ?>" data-name="<?php echo h($user['User']['name']) ?>" style="cursor:pointer">
            <?php echo h($user['User']['name']) . " " . h($user['User']['surname']) . " -> " . h($user['Contact']['email']); ?>
        </div>
    <?php } ?>
</div>