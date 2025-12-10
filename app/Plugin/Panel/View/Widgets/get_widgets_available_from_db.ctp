<?php foreach ($available_widgets as $available_widget) {?>
    <div class="create" id="<?php echo $available_widget['PanelWidget']['id'];?>">
        <?php echo $available_widget['PanelWidget']['display_name'];?>
    </div>
<?php } ?>
