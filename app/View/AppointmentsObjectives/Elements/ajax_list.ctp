<div class="medium-12 columns" style="padding: 0 !important;">
    <div id="List2" class="connectedSortable">
        <?php foreach ($distributors as $distributor){ ?>
            <div class="ui-state-highlight" data-id="<?php echo $distributor['Distributor']['id']?>">
                <?php
					echo h($distributor['Distributor']['name']) . " - " . h($distributor['Distributor']['account_number']);
				?>
            </div>
        <?php } ?>
    </div>
</div>
