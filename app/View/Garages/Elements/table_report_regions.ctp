<div class="o-auto ampliacion" style="padding: 15px 0 25px 0;">
    <table class="table-tracking">
        <thead>
        <tr>
            <th class="text-left"><?php echo 'Regions' ?></th>
            <?php
            foreach ($networks as $network)
            {
                ?> <th class="text-center"><?php echo $network['Network']['name'] ?></th> <?php
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($regions as $region)
        {
            ?>
            <tr>
                <?php
                if(is_array($region))
                {
                    ?>
                    <td class="text-left">
                        <?php echo $region['AagRegion']['name']; ?>
                    </td>
                    <?php
                    foreach($region['Networks'] as $key => $network)
                    {
                        ?>
                        <td class="text-center">
                            <?php
                            if($network['associates'] != 0)
                            {
                                echo "<div class= 'paso-n green'><span>";
                                echo $network['associates'];
                                echo "</span></div>";
                            }
                            else
                            {
                                echo $network['associates'];
                            }
                            ?>
                        </td>
                        <?php
                    }
                }
                ?>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>