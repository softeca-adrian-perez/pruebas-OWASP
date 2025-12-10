<?php foreach ($tasks_garages_completed as $task) { ?>
    <tr>
        <td class="c-defecto ta-center">
            <?php echo h($task['Task']['title']); ?>
        </td>
        <td class="c-defecto fw-bold ta-center">
            <?php echo h($task['Task']['creation_date']); ?>
        </td>
        <td class="c-defecto fw-bold ta-center">
            <?php echo h($task['TaskGarage']['completed_date']); ?>
        </td>
    </tr>
<?php } ?>