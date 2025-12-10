<div class="medium-12 columns" style="padding: 0 !important;">
    <div id="List2" class="connectedSortable">
        <?php
            foreach ($contacts as $index => $contact) {
                if ($index < 100) {
                    echo '<div class="ui-state-highlight" data-id="' . $contact['Contact']['id'] . '">';
                    echo h($contact['Contact']['first_name']) . ' ' . h($contact['Contact']['last_name']);
                    echo '</div>';
                }
            }
        ?>
    </div>
</div>